<?php

namespace App\Services\Backup;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;

/**
 * One backup run:
 *
 *   lock -> (if-due check) -> /start -> dump -> gzip -> verify -> sha256
 *   -> split -> safety check -> upload to the day's release -> one commit
 *   per backup (its .sha256) -> prune commits + delete old releases -> success
 *
 * Nothing is written to the dumps repo until the dump has been made and
 * verified, so any failure before the commit leaves the remote (and the
 * previous backup) untouched. Every failure path pings /fail.
 */
class BackupService
{
    public const GZIP_MAGIC = "\x1f\x8b";

    private ?string $workDir = null;

    public function __construct(
        private array $config,
        private BackupPaths $paths,
        private Healthcheck $healthcheck,
        private BackupLock $lock,
        private \Closure $github,   // fn(): GitHubClient (built lazily: config errors become /fail pings)
        private \Closure $dumper,   // fn(): DatabaseDumper
    ) {
    }

    public static function fromConfig(): self
    {
        $c = config('dump-github');

        return new self(
            $c,
            BackupPaths::fromConfig(),
            new Healthcheck($c['healthcheck_url']),
            new BackupLock(storage_path('app/backup.lock')),
            fn () => GitHubClient::fromConfig(),
            fn () => DatabaseDumper::fromConfig(),
        );
    }

    /**
     * @param  bool|null  $withFiles  null = only on the weekly files slot
     * @param  bool  $ifDue  skip (status "up-to-date") when this slot is already in the repo
     * @return array{status: string, slot: string, files: string[], notes: string[]}
     */
    public function run(?bool $withFiles = null, bool $ifDue = false, ?CarbonImmutable $now = null, string $trigger = 'cli'): array
    {
        $now ??= CarbonImmutable::now();
        $slot = $this->paths->slotFor($now);
        $slotName = $slot->format('m-d H:i T');

        if (! $this->config['enabled']) {
            throw new BackupException('backups are disabled (DUMP_ENABLED=false)');
        }

        $this->lock->acquire(); // BackupLockedException: no ping, the other run reports
        $started = false;
        try {
            $gh = ($this->github)();
            $head = $gh->headCommit();
            $blobs = $head ? $gh->listBlobs($gh->commitTree($head)) : [];

            $withFiles ??= $this->config['files']['enabled']
                && (int) $slot->format('N') === $this->config['files']['weekday']
                && (int) $slot->format('G') === $this->config['files']['slot'];

            if ($ifDue && $this->slotDone($gh, $blobs, $slot, $withFiles)) {
                return ['status' => 'up-to-date', 'slot' => $slotName, 'files' => [], 'notes' => []];
            }

            $this->healthcheck->start();
            $started = true;
            Log::info("backup: starting slot $slotName (trigger=$trigger, with-files=" . ($withFiles ? 'yes' : 'no') . ')');

            $this->workDir = $this->makeWorkDir();
            $notes = [];

            // 1. Build every artefact locally before touching the remote.
            $dumper = ($this->dumper)();
            $sql = $this->workDir . '/dump.sql';
            $dumper->dump($sql);
            $notes = array_merge($notes, $dumper->notes, ["dump method: {$dumper->methodUsed}"]);
            $backups = [$this->package($sql, $this->paths->dbStem($slot), 'Dump completed')];

            if ($withFiles) {
                $tar = $this->workDir . '/files.tar';
                $archiver = new FilesArchiver();
                $archiver->archive($this->config['files']['root'], $this->config['files']['exclude'], $tar . '.gz');
                $notes[] = "files archived: {$archiver->fileCount}";
                $backups[] = $this->package($tar . '.gz', $this->paths->filesStem($slot), null, true);
            }

            // 2. Upload: one commit per backup, replacing whatever the slot held.
            if ($head === null) {
                $head = $gh->bootstrap($this->config['author']['name'], $this->config['author']['email']);
                $blobs = $gh->listBlobs($gh->commitTree($head));
            }
            $uploaded = [];
            foreach ($backups as $b) {
                $head = $this->commitBackup($gh, $head, $blobs, $b, self::releaseTag($slot));
                $uploaded[] = $b['stem'];
            }

            // 3. Prune.
            $blobs = $gh->listBlobs($gh->commitTree($head));
            $pruned = $this->prune($gh, $head, $blobs, $now);
            if ($pruned) {
                $notes[] = 'pruned: ' . implode(', ', $pruned);
            }

            $notes[] = sprintf('peak PHP memory: %.1f MiB (memory_limit %s)', memory_get_peak_usage(true) / 1048576, ini_get('memory_limit'));
            $summary = "slot $slotName OK on " . gethostname() . "\n" . implode("\n", array_merge($uploaded, $notes));
            $this->healthcheck->success($summary);
            Log::info('backup: ' . str_replace("\n", '; ', $summary));

            return ['status' => 'ok', 'slot' => $slotName, 'files' => $uploaded, 'notes' => $notes];
        } catch (\Throwable $e) {
            $msg = "slot $slotName FAILED on " . gethostname() . ': ' . $e->getMessage();
            Log::error('backup: ' . $msg);
            // /fail even if /start was never sent (e.g. GitHub unreachable during the due check).
            $this->healthcheck->fail($msg . ($started ? '' : ' (before /start)'));
            throw $e instanceof BackupException ? $e : new BackupException($e->getMessage(), 0, $e);
        } finally {
            if ($this->workDir) {
                FileTools::removeTree($this->workDir);
                $this->workDir = null;
            }
            $this->lock->release();
        }
    }

    /** Release that holds a slot's dump files: one per day, e.g. backup-2026-11-22. */
    public static function releaseTag(CarbonImmutable $day): string
    {
        return 'backup-' . $day->format('Y-m-d');
    }

    /** @param array<string, array{sha: string, size: int}> $blobs */
    private function slotDone(GitHubClient $gh, array $blobs, CarbonImmutable $slot, bool $withFiles): bool
    {
        $stems = [$this->paths->dbStem($slot)];
        if ($withFiles) {
            $stems[] = $this->paths->filesStem($slot);
        }
        foreach ($stems as $stem) {
            if (! isset($blobs[$stem . '.sha256'])) {
                return false;
            }
        }
        // The checksums are committed; the data must be in the day's release too.
        $release = $gh->releaseByTag(self::releaseTag($slot));
        $names = array_column($release['assets'] ?? [], 'name');
        foreach ($stems as $stem) {
            $base = basename($stem);
            if (! in_array($base, $names, true) && ! in_array($base . '.part000', $names, true)) {
                return false;
            }
            foreach ($names as $n) {
                if (str_starts_with($n, $base . '.') && str_ends_with($n, '.uploading')) {
                    return false; // an earlier run stopped half-way: redo the slot
                }
            }
        }

        return true;
    }

    /**
     * gzip (if needed), verify, hash, split.
     *
     * @return array{stem: string, files: array<string, string>}  repo path => local file
     */
    private function package(string $file, string $stem, ?string $footer, bool $alreadyGz = false): array
    {
        $local = $this->workDir . '/' . basename($stem);
        if ($alreadyGz) {
            rename($file, $local);
        } else {
            FileTools::gzip($file, $local);
            unlink($file);
        }
        $tail = FileTools::testGzip($local);
        if ($footer !== null && ! str_contains($tail, $footer)) {
            throw new BackupException(basename($stem) . " failed verification: no \"$footer\" at the end");
        }

        $sha = FileTools::sha256($local);
        $shaFile = $local . '.sha256';
        file_put_contents($shaFile, $sha . '  ' . basename($stem) . "\n");

        $files = [];
        foreach (FileTools::split($local, $this->splitBytes()) as $part) {
            $files[$stem . substr($part, strlen($local))] = $part;
        }
        $files[$stem . '.sha256'] = $shaFile;

        return ['stem' => $stem, 'files' => $files];
    }

    /**
     * Refuse to commit anything that isn't a backup artefact: wrong path,
     * a data file that isn't gzip, a malformed .sha256, or a blob GitHub
     * would reject.
     *
     * @param  array<string, string>  $files  repo path => local file
     */
    public function safetyCheck(array $files): void
    {
        foreach ($files as $path => $local) {
            $p = $this->paths->parse($path);
            if ($p === null) {
                throw new BackupException("safety check: refusing to commit '$path' (not a backup artefact)");
            }
            if (filesize($local) > $this->splitBytes()) {
                throw new BackupException("safety check: '$path' is larger than the split size");
            }
            if ($p['suffix'] === '.sha256') {
                $want = '/^[0-9a-f]{64}  ' . preg_quote(basename($p['stem']), '/') . '\n$/';
                if (! preg_match($want, (string) file_get_contents($local, false, null, 0, 1000))) {
                    throw new BackupException("safety check: '$path' is not a checksum file");
                }
            } elseif ($p['suffix'] === '' || $p['suffix'] === '.part000') {
                if (! FileTools::startsWith($local, self::GZIP_MAGIC)) {
                    throw new BackupException("safety check: '$path' does not start with a gzip header");
                }
            }
        }
    }

    /**
     * Upload the dump files to the day's release, commit the .sha256, then
     * swap the files in:
     *
     *   1. upload every file as "<name>.uploading"
     *   2. commit the new .sha256 (the "chore(db): backup" commit)
     *   3. delete the slot's old files and rename the new ones
     *
     * A failure in 1 or 2 leaves the previous backup and its checksum
     * untouched. A failure in 3 leaves ".uploading" files behind: the slot
     * then counts as not done, so the next run redoes it, and until then a
     * restore fails the checksum loudly instead of restoring the wrong file.
     *
     * @param  array<string, array{sha: string, size: int}>  $blobs
     * @param  array{stem: string, files: array<string, string>}  $backup
     */
    private function commitBackup(GitHubClient $gh, string $head, array $blobs, array $backup, string $tag): string
    {
        $this->safetyCheck($backup['files']);
        $stemName = basename($backup['stem']);
        $isMine = fn (string $name) => $name === $stemName || str_starts_with($name, $stemName . '.');

        $release = $gh->releaseByTag($tag) ?? $gh->createRelease($tag, $head,
            "Database backups for {$tag} (Asia/Kathmandu), uploaded by newdrm (DRM). "
            . "Each file's checksum is committed under backups/. Deleted automatically after "
            . $this->config['retention_days'] . ' days.');

        // Leftovers of an earlier failed upload.
        foreach ($release['assets'] ?? [] as $a) {
            if ($isMine($a['name']) && str_ends_with($a['name'], '.uploading')) {
                $gh->deleteAsset($a['id']);
            }
        }

        // 1. upload under temporary names
        $new = [];
        foreach ($backup['files'] as $path => $local) {
            if (str_ends_with($path, '.sha256')) {
                continue;
            }
            $asset = $gh->uploadAsset($release['id'], basename($path) . '.uploading', $local);
            $new[basename($path)] = $asset['id'];
        }

        // 2. commit the checksum
        $sha = $backup['files'][$backup['stem'] . '.sha256'];
        $entries = [['path' => $backup['stem'] . '.sha256', 'sha' => $gh->createBlobFromFile($sha)]];
        // Data files committed by an older layout: drop them from the tree.
        foreach (array_keys($this->paths->group($blobs)[$backup['stem']]['members'] ?? []) as $path) {
            if (! str_ends_with($path, '.sha256')) {
                $entries[] = ['path' => $path, 'sha' => null];
            }
        }
        $head = $this->commit($gh, $head, $entries, 'chore(db): backup ' . substr($backup['stem'], strlen($this->config['path']) + 1));

        // 3. swap: drop the slot's old file/parts, give the new ones their names
        foreach ($release['assets'] ?? [] as $a) {
            if ($isMine($a['name']) && ! str_ends_with($a['name'], '.uploading')) {
                $gh->deleteAsset($a['id']);
            }
        }
        foreach ($new as $name => $id) {
            $gh->renameAsset($id, $name);
        }

        return $head;
    }

    /**
     * The roll: one commit per expired backup (removing its .sha256), then
     * delete the releases older than the retention, which really frees the
     * space. Git history only ever holds the small checksum files.
     *
     * @param  array<string, array{sha: string, size: int}>  $blobs
     * @return string[] what was pruned
     */
    private function prune(GitHubClient $gh, string &$head, array $blobs, CarbonImmutable $now): array
    {
        $pruned = [];
        $groups = $this->paths->group($blobs);
        ksort($groups);
        foreach ($groups as $stem => $g) {
            if (! $this->paths->isExpired($g['md'], $now, $this->config['retention_days'])) {
                continue;
            }
            $entries = array_map(fn ($p) => ['path' => $p, 'sha' => null], array_keys($g['members']));
            $head = $this->commit($gh, $head, $entries, "chore(db): prune expired backup $stem");
            $pruned[] = $stem;
        }

        $today = $now->setTimezone($this->config['timezone'])->startOfDay();
        foreach ($gh->listReleases() as $r) {
            if (! preg_match('/^backup-(\d{4}-\d{2}-\d{2})$/', $r['tag_name'], $m)) {
                continue; // not ours: never touch it
            }
            $day = CarbonImmutable::createFromFormat('!Y-m-d', $m[1], $this->config['timezone']);
            if ($day && $day < $today && (int) $day->diffInDays($today) > $this->config['retention_days']) {
                $gh->deleteRelease($r['id'], $r['tag_name']);
                $pruned[] = 'release ' . $r['tag_name'];
            }
        }

        return $pruned;
    }

    private function commit(GitHubClient $gh, string $head, array $entries, string $message): string
    {
        foreach ($entries as $e) {
            if (! $this->paths->isArtefact($e['path'])) {
                throw new BackupException("safety check: refusing to touch '{$e['path']}'");
            }
        }
        $tree = $gh->createTree($gh->commitTree($head), $entries);
        $commit = $gh->createCommit($message, $tree, [$head], $this->config['author']['name'], $this->config['author']['email']);
        $gh->updateRef($commit);

        return $commit;
    }

    /** The configured split size, never above what GitHub accepts. */
    private function splitBytes(): int
    {
        return max(1024, min((int) $this->config['split_bytes'], GitHubClient::MAX_ASSET_BYTES));
    }

    private function makeWorkDir(): string
    {
        $base = $this->config['tmp_dir'];
        if (! is_dir($base) && ! @mkdir($base, 0700, true) && ! is_dir($base)) {
            throw new BackupException("cannot create temp dir $base");
        }
        $dir = $base . '/run-' . bin2hex(random_bytes(6));
        if (! mkdir($dir, 0700)) {
            throw new BackupException("cannot create $dir");
        }

        return $dir;
    }
}
