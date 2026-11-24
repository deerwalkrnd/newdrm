<?php

namespace App\Services\Backup;

use Carbon\CarbonImmutable;

/**
 * Read side for the Backups page: list backups and stream one back.
 *
 * A backup is complete when its .sha256 is committed under backups/MM-DD/
 * and its file (or parts) is attached to that day's release.
 */
class BackupCatalog
{
    public function __construct(private GitHubClient $gh, private BackupPaths $paths)
    {
    }

    public static function fromConfig(): self
    {
        return new self(GitHubClient::fromConfig(), BackupPaths::fromConfig());
    }

    /**
     * Newest first.
     *
     * @return array<int, array{stem: string, name: string, kind: string, date: ?CarbonImmutable, size: int, parts: int, complete: bool, data: array<string, int>}>
     */
    public function list(?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $head = $this->gh->headCommit();
        if ($head === null) {
            return [];
        }
        $blobs = $this->gh->listBlobs($this->gh->commitTree($head));

        // tag => [asset name => asset]
        $assets = [];
        foreach ($this->gh->listReleases() as $r) {
            foreach ($r['assets'] ?? [] as $a) {
                $assets[$r['tag_name']][$a['name']] = $a;
            }
        }

        $out = [];
        foreach ($this->paths->group($blobs) as $stem => $g) {
            $day = $this->paths->resolveDate($g['md'], $now);
            $tag = $day ? BackupService::releaseTag($day) : null;
            $base = basename($stem);
            $data = [];
            $size = 0;
            $files = $tag ? ($assets[$tag] ?? []) : [];
            ksort($files);
            $pending = false; // a run stopped between commit and swap
            foreach ($files as $name => $a) {
                if ($name === $base || preg_match('/^' . preg_quote($base, '/') . '\.part\d{3}$/', $name)) {
                    $data[$name] = (int) $a['id'];
                    $size += (int) $a['size'];
                } elseif (str_starts_with($name, $base . '.') && str_ends_with($name, '.uploading')) {
                    $pending = true;
                }
            }
            $out[] = [
                'stem' => $stem,
                'name' => $base,
                'kind' => $g['kind'],
                'date' => $day?->setTime($g['hour'], 0),
                'size' => $size,
                'parts' => count($data),
                'complete' => isset($g['members'][$stem . '.sha256']) && $data !== [] && ! $pending,
                'data' => $data, // asset name => asset id, in part order
            ];
        }
        usort($out, fn ($a, $b) => [$b['date']?->getTimestamp() ?? 0, $b['kind'] === 'db'] <=> [$a['date']?->getTimestamp() ?? 0, $a['kind'] === 'db']);

        return $out;
    }

    public function latest(string $kind = 'db'): ?array
    {
        foreach ($this->list() as $b) {
            if ($b['kind'] === $kind && $b['complete']) {
                return $b;
            }
        }

        return null;
    }

    public function find(string $stem): ?array
    {
        foreach ($this->list() as $b) {
            if ($b['stem'] === $stem) {
                return $b;
            }
        }

        return null;
    }

    /** Write the backup's parts, joined, to the output (or $out). */
    public function stream(array $backup, $out = null): void
    {
        $out ??= fopen('php://output', 'wb');
        foreach ($backup['data'] as $assetId) {
            $body = $this->gh->assetStream($assetId);
            while (! $body->eof()) {
                fwrite($out, $body->read(FileTools::CHUNK));
                if (function_exists('ob_get_level') && ob_get_level() > 0) {
                    @ob_flush();
                }
                flush();
            }
            $body->close();
        }
    }
}
