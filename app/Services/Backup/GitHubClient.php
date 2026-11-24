<?php

namespace App\Services\Backup;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * The few GitHub REST endpoints the backups need. No git binary, no clone.
 *
 * Uploads go through the Git Data API (blobs -> tree -> commit -> ref) so a
 * file, its parts and its .sha256 land in one commit, and so a slot can be
 * replaced (old parts deleted) in that same commit.
 *
 * Blob bodies are built on disk with base64 written in chunks and streamed
 * to GitHub, so a 90 MB part never sits in PHP memory.
 */
class GitHubClient
{
    /**
     * Largest git blob the API accepted when measured (40 MiB worked, 45 MiB
     * was refused with HTTP 422 "input too large"; the cap is on the base64
     * request). Only the small .sha256 files go through blobs now.
     */
    public const MAX_BLOB_BYTES = 40 * 1024 * 1024;

    /** GitHub's limit for one release asset is 2 GiB; stay under it. */
    public const MAX_ASSET_BYTES = 1900 * 1000 * 1000;

    private Client $http;

    private string $repoPath;

    private string $uploadsUrl;

    public function __construct(
        private string $token,
        private string $owner,
        private string $repo,
        private string $branch,
        private string $apiUrl,
        private string $tmpDir,
        int $timeout = 600,
        ?Client $http = null,
        ?string $uploadsUrl = null,
    ) {
        // Release assets are uploaded to uploads.github.com, not api.github.com.
        $this->uploadsUrl = rtrim($uploadsUrl ?? (str_contains($apiUrl, '://api.github.com')
            ? 'https://uploads.github.com' : $apiUrl), '/');
        $this->repoPath = '/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo);
        $this->http = $http ?? new Client([
            'base_uri' => $apiUrl,
            'timeout' => $timeout,
            'connect_timeout' => 20,
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent' => 'newdrm-backup',
            ],
        ]);
    }

    public static function fromConfig(): self
    {
        $c = config('dump-github');
        if (empty($c['github']['token'])) {
            throw new BackupException('DUMP_GITHUB_TOKEN is not set');
        }

        return new self(
            $c['github']['token'],
            $c['github']['owner'],
            $c['github']['repo'],
            $c['github']['branch'],
            $c['github']['api_url'],
            $c['tmp_dir'],
            $c['github']['timeout'],
            null,
            $c['github']['uploads_url'] ?: null,
        );
    }

    public function repoName(): string
    {
        return $this->owner . '/' . $this->repo;
    }

    /** Head commit of the branch, or null if the repo/branch is empty. */
    public function headCommit(): ?string
    {
        $res = $this->request('GET', $this->repoPath . '/git/ref/heads/' . $this->branchPath(), [], [404, 409]);
        if (in_array($res->getStatusCode(), [404, 409], true)) {
            return null;
        }

        return $this->json($res)['object']['sha'];
    }

    public function commitTree(string $commitSha): string
    {
        return $this->json($this->request('GET', $this->repoPath . '/git/commits/' . $commitSha))['tree']['sha'];
    }

    /**
     * Every blob in the tree, recursively.
     *
     * @return array<string, array{sha: string, size: int}>
     */
    public function listBlobs(string $treeSha): array
    {
        $data = $this->json($this->request('GET', $this->repoPath . '/git/trees/' . $treeSha, ['query' => ['recursive' => '1']]));
        if (! empty($data['truncated'])) {
            throw new BackupException('GitHub returned a truncated tree; the dumps repo has too many files');
        }
        $out = [];
        foreach ($data['tree'] as $e) {
            if ($e['type'] === 'blob') {
                $out[$e['path']] = ['sha' => $e['sha'], 'size' => (int) ($e['size'] ?? 0)];
            }
        }

        return $out;
    }

    /**
     * Upload a local file as a blob. The JSON body is written to a temp file
     * with base64 produced 768 KiB at a time, then streamed.
     */
    public function createBlobFromFile(string $file): string
    {
        $body = tempnam($this->tmpDir, 'blob-');
        try {
            $in = fopen($file, 'rb');
            $out = fopen($body, 'wb');
            if (! $in || ! $out) {
                throw new BackupException("cannot open $file for upload");
            }
            fwrite($out, '{"encoding":"base64","content":"');
            while (! feof($in)) {
                $chunk = fread($in, 3 * 262144); // multiple of 3: no padding mid-stream
                if ($chunk === false) {
                    throw new BackupException("read error on $file");
                }
                if ($chunk !== '') {
                    fwrite($out, base64_encode($chunk));
                }
            }
            fwrite($out, '"}');
            fclose($in);
            fclose($out);

            $stream = Utils::streamFor(fopen($body, 'rb'));
            $res = $this->request('POST', $this->repoPath . '/git/blobs', [
                'body' => $stream,
                'headers' => ['Content-Type' => 'application/json'],
            ]);
            $sha = $this->json($res)['sha'];
            $stream->close();

            return $sha;
        } finally {
            @unlink($body);
        }
    }

    /**
     * @param  array<int, array{path: string, sha: ?string}>  $entries  sha null deletes the path
     */
    public function createTree(?string $baseTree, array $entries): string
    {
        $payload = ['tree' => array_map(fn ($e) => [
            'path' => $e['path'],
            'mode' => '100644',
            'type' => 'blob',
            'sha' => $e['sha'],
        ], $entries)];
        if ($baseTree !== null) {
            $payload['base_tree'] = $baseTree;
        }

        return $this->json($this->request('POST', $this->repoPath . '/git/trees', ['json' => $payload]))['sha'];
    }

    public function createCommit(string $message, string $tree, array $parents, string $authorName, string $authorEmail): string
    {
        $who = ['name' => $authorName, 'email' => $authorEmail, 'date' => gmdate('Y-m-d\TH:i:s\Z')];

        return $this->json($this->request('POST', $this->repoPath . '/git/commits', ['json' => [
            'message' => $message,
            'tree' => $tree,
            'parents' => $parents,
            'author' => $who,
            'committer' => $who,
        ]]))['sha'];
    }

    /** Fast-forward only: a concurrent writer makes this fail instead of being overwritten. */
    public function updateRef(string $commitSha): void
    {
        $this->request('PATCH', $this->repoPath . '/git/refs/heads/' . $this->branchPath(), [
            'json' => ['sha' => $commitSha, 'force' => false],
        ]);
    }

    /**
     * The Git Data API can't write to an empty repo, so the first run
     * creates README.md through the Contents API.
     */
    public function bootstrap(string $authorName, string $authorEmail): string
    {
        $readme = "# Database backups\n\nWritten by newdrm's in-app backup job.\n\n"
            . "- The dump files are on the **Releases** page: one release per day (`backup-YYYY-MM-DD`).\n"
            . "- Each file's checksum is committed under `backups/MM-DD/`.\n"
            . "- Backups older than 30 days are pruned automatically, releases included.\n\n"
            . "Restoring: see scripts/backup/README.md in the app repo.\n";
        // No "branch": an empty repo has none yet, and GitHub creates the
        // repo's default branch for this first commit.
        $this->request('PUT', $this->repoPath . '/contents/README.md', ['json' => [
            'message' => 'chore(db): initialise dumps repo',
            'content' => base64_encode($readme),
            'committer' => ['name' => $authorName, 'email' => $authorEmail],
        ]]);
        $head = $this->headCommit();
        if ($head === null) {
            throw new BackupException("created README.md, but branch '{$this->branch}' still doesn't exist: "
                . 'set DUMP_GITHUB_BRANCH to the dumps repo\'s default branch');
        }

        return $head;
    }

    /** Raw blob contents as a stream (GitHub serves blobs up to 100 MB this way). */
    public function blobStream(string $sha): StreamInterface
    {
        return $this->request('GET', $this->repoPath . '/git/blobs/' . $sha, [
            'headers' => ['Accept' => 'application/vnd.github.raw+json'],
            'stream' => true,
        ])->getBody();
    }

    // ---- releases: where the dump files live ------------------------------

    /** @return array{id: int, tag_name: string, assets: array}|null */
    public function releaseByTag(string $tag): ?array
    {
        $res = $this->request('GET', $this->repoPath . '/releases/tags/' . rawurlencode($tag), [], [404]);

        return $res->getStatusCode() === 404 ? null : $this->json($res);
    }

    /** Every release, newest first (paginated). */
    public function listReleases(): array
    {
        $all = [];
        for ($page = 1; $page <= 50; $page++) {
            $batch = $this->json($this->request('GET', $this->repoPath . '/releases', ['query' => ['per_page' => 100, 'page' => $page]]));
            $all = array_merge($all, $batch);
            if (count($batch) < 100) {
                break;
            }
        }

        return $all;
    }

    public function createRelease(string $tag, string $target, string $body): array
    {
        try {
            return $this->json($this->request('POST', $this->repoPath . '/releases', ['json' => [
                'tag_name' => $tag,
                'target_commitish' => $target,
                'name' => $tag,
                'body' => $body,
                'make_latest' => 'false',
            ]]));
        } catch (BackupException $e) {
            // A retried create whose first attempt did go through.
            if ($e->getCode() === 422 && ($r = $this->releaseByTag($tag))) {
                return $r;
            }
            throw $e;
        }
    }

    /** Streams the file as the request body: no base64, no copy in memory. */
    /**
     * Streams the file as the request body: no base64, no copy in memory.
     * A dropped connection or a 5xx is retried; a half-uploaded asset left
     * by the failed attempt is deleted first (else the name is "taken").
     */
    public function uploadAsset(int $releaseId, string $name, string $file): array
    {
        for ($attempt = 0; ; $attempt++) {
            $fh = fopen($file, 'rb');
            if (! $fh) {
                throw new BackupException("cannot open $file for upload");
            }
            $stream = Utils::streamFor($fh);
            try {
                $res = $this->request('POST', $this->uploadsUrl . $this->repoPath . '/releases/' . $releaseId . '/assets', [
                    'query' => ['name' => $name],
                    'body' => $stream,
                    'headers' => ['Content-Type' => 'application/octet-stream', 'Content-Length' => (string) filesize($file)],
                ], [], false);

                return $this->json($res);
            } catch (BackupException $e) {
                $transient = $e->getCode() === 0 || $e->getCode() >= 500;
                if (! $transient || $attempt >= count($this->retryDelays)) {
                    throw $e;
                }
                sleep($this->retryDelays[$attempt]);
                $this->deleteAssetNamed($releaseId, $name);
            } finally {
                $stream->close();
            }
        }
    }

    private function deleteAssetNamed(int $releaseId, string $name): void
    {
        $release = $this->json($this->request('GET', $this->repoPath . '/releases/' . $releaseId));
        foreach ($release['assets'] ?? [] as $a) {
            if ($a['name'] === $name) {
                $this->deleteAsset((int) $a['id']);
            }
        }
    }

    public function renameAsset(int $assetId, string $name): void
    {
        $this->request('PATCH', $this->repoPath . '/releases/assets/' . $assetId, ['json' => ['name' => $name]]);
    }

    public function deleteAsset(int $assetId): void
    {
        $this->request('DELETE', $this->repoPath . '/releases/assets/' . $assetId, [], [404]);
    }

    /** Deletes the release and its tag; missing ones are fine. */
    public function deleteRelease(int $releaseId, string $tag): void
    {
        $this->request('DELETE', $this->repoPath . '/releases/' . $releaseId, [], [404]);
        $this->request('DELETE', $this->repoPath . '/git/refs/tags/' . rawurlencode($tag), [], [404, 422]);
    }

    /**
     * Asset contents. GitHub answers with a redirect to its file storage;
     * Guzzle follows it and drops the Authorization header for the other host.
     */
    public function assetStream(int $assetId): StreamInterface
    {
        return $this->request('GET', $this->repoPath . '/releases/assets/' . $assetId, [
            'headers' => ['Accept' => 'application/octet-stream'],
            'stream' => true,
            'allow_redirects' => ['max' => 5, 'strict' => false, 'referer' => false, 'protocols' => ['https', 'http']],
        ])->getBody();
    }

    private function branchPath(): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $this->branch)));
    }

    /** Seconds to wait before each retry of a dropped connection or a GitHub 5xx. */
    public array $retryDelays = [2, 6];

    private function request(string $method, string $uri, array $options = [], array $okStatuses = [], bool $retry = true): ResponseInterface
    {
        $delays = $retry ? $this->retryDelays : [];
        for ($attempt = 0; ; $attempt++) {
            if (isset($options['body']) && $options['body'] instanceof StreamInterface && $options['body']->isSeekable()) {
                $options['body']->rewind();
            }
            try {
                $res = $this->http->request($method, $uri, $options);
            } catch (GuzzleException $e) {
                if ($attempt < count($delays)) {
                    sleep($delays[$attempt]);
                    continue;
                }
                throw new BackupException("GitHub $method " . $this->redact($uri) . ' failed: ' . $e->getMessage(), 0, $e);
            }
            if (in_array($res->getStatusCode(), [500, 502, 503, 504], true) && $attempt < count($delays)) {
                sleep($delays[$attempt]);
                continue;
            }
            break;
        }
        $code = $res->getStatusCode();
        if ($code >= 300 && ! in_array($code, $okStatuses, true)) {
            $msg = '';
            if (empty($options['stream'])) {
                $data = json_decode((string) $res->getBody(), true);
                $msg = is_array($data) ? ($data['message'] ?? '') : '';
            }
            throw new BackupException("GitHub $method " . $this->redact($uri) . " returned HTTP $code" . ($msg !== '' ? ": $msg" : ''), $code);
        }

        return $res;
    }

    private function json(ResponseInterface $res): array
    {
        $data = json_decode((string) $res->getBody(), true);
        if (! is_array($data)) {
            throw new BackupException('GitHub returned a non-JSON response (HTTP ' . $res->getStatusCode() . ')');
        }

        return $data;
    }

    private function redact(string $uri): string
    {
        return str_replace($this->token, '***', $uri);
    }
}
