<?php

/**
 * A local fake of the GitHub REST endpoints the backup job uses, backed by a
 * bare git repo. Run with:  php -S 127.0.0.1:9999 router.php
 *
 * Env:  MOCK_REPO   bare repo path        MOCK_TOKEN  required bearer token
 *       MOCK_DIR    state dir (log, fault file)
 *
 * Fault injection: write "<status> <METHOD> <path regex>" lines to
 * $MOCK_DIR/fault (e.g. "422 POST #/git/trees$#"). Every request is logged
 * to $MOCK_DIR/requests.log as "METHOD path status".
 *
 * Mirrors real GitHub where it matters here: the Git Data API refuses an
 * empty repo (409), blobs over 100 MB are refused, ref updates must be
 * fast-forward unless forced.
 */

ini_set('memory_limit', '-1');
$repo = getenv('MOCK_REPO');
$dir = getenv('MOCK_DIR');
$token = getenv('MOCK_TOKEN');
$branch = 'main';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function reply(int $code, $data = null, ?string $raw = null): void
{
    global $method, $uri, $dir;
    file_put_contents("$dir/requests.log", "$method $uri $code\n", FILE_APPEND | LOCK_EX);
    http_response_code($code);
    if ($raw !== null) {
        return;
    }
    header('Content-Type: application/json');
    echo json_encode($data ?? new stdClass());
    exit;
}

function git(array $args, ?string $stdin = null, array $env = []): string
{
    global $repo;
    @mkdir('/tmp/mock-wt');
    $cmd = array_merge(['git', '--git-dir=' . $repo, '--work-tree=/tmp/mock-wt'], $args);
    $p = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, array_merge(getenv(), $env));
    if ($stdin !== null) {
        fwrite($pipes[0], $stdin);
    }
    fclose($pipes[0]);
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    $code = proc_close($p);
    if ($code !== 0) {
        throw new RuntimeException('git ' . implode(' ', $args) . " failed: $err");
    }

    return trim($out);
}

function head(): ?string
{
    global $branch;
    try {
        return git(['rev-parse', '--verify', '-q', "refs/heads/$branch"]);
    } catch (RuntimeException $e) {
        return null;
    }
}

function body(): array
{
    $d = json_decode(file_get_contents('php://input'), true);
    if (! is_array($d)) {
        reply(400, ['message' => 'Problems parsing JSON']);
    }

    return $d;
}

function blobFromBytes(string $bytes): string
{
    if (strlen($bytes) > 100 * 1024 * 1024) {
        reply(422, ['message' => 'blob too large (over 100 MB)']);
    }

    return git(['hash-object', '-w', '--stdin'], $bytes);
}

// --- fake healthchecks.io: /hc/<uuid>[/start|/fail] ------------------------
if (preg_match('#^/hc/[^/]+(/start|/fail)?$#', $uri, $hm)) {
    $kind = ($hm[1] ?? '') === '' ? 'success' : ltrim($hm[1], '/');
    $msg = str_replace("\n", ' | ', substr(file_get_contents('php://input'), 0, 300));
    file_put_contents("$dir/hc.log", "$kind $msg\n", FILE_APPEND | LOCK_EX);
    http_response_code(200);
    echo 'OK';
    exit;
}

// --- fake file storage that release-asset downloads redirect to ------------
// Like GitHub's real storage host: a different host name, and a request that
// still carries the API token is refused.
if (preg_match('#^/_storage/(\d+)$#', $uri, $sm)) {
    if (! empty($_SERVER['HTTP_AUTHORIZATION'])) {
        reply(400, ['message' => 'storage: Authorization header not allowed (only one auth mechanism)']);
    }
    $f = "$dir/assets/{$sm[1]}";
    if (! is_file($f)) {
        reply(404, ['message' => 'Not Found']);
    }
    file_put_contents("$dir/requests.log", "GET $uri 200\n", FILE_APPEND | LOCK_EX);
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($f));
    readfile($f);
    exit;
}

// --- auth + faults ---------------------------------------------------------
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($auth !== "Bearer $token") {
    reply(401, ['message' => 'Bad credentials']);
}
// $MOCK_DIR/fault-once: same format, but each line fires once and is removed
// (to simulate a passing GitHub hiccup, e.g. a 502 on one upload).
if (is_file("$dir/fault-once")) {
    $lines = file("$dir/fault-once", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $i => $line) {
        [$code, $m, $re] = explode(' ', $line, 3);
        if (($m === '*' || $m === $method) && preg_match($re, $uri)) {
            unset($lines[$i]);
            file_put_contents("$dir/fault-once", implode("
", $lines));
            file_put_contents("$dir/requests.log", "$method $uri fault-once $code
", FILE_APPEND | LOCK_EX);
            reply((int) $code, ['message' => "injected one-off fault $code"]);
        }
    }
}
if (is_file("$dir/fault")) {
    foreach (file("$dir/fault", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        [$code, $m, $re] = explode(' ', $line, 3);
        if (($m === '*' || $m === $method) && preg_match($re, $uri)) {
            reply((int) $code, ['message' => "injected fault $code"]);
        }
    }
}

if (! preg_match('#^/repos/([^/]+)/([^/]+)(/.*)$#', $uri, $m)) {
    reply(404, ['message' => 'Not Found']);
}
$path = $m[3];

try {
    // GET /git/ref/heads/main
    if ($method === 'GET' && $path === "/git/ref/heads/$branch") {
        $h = head();
        if ($h === null) {
            reply(409, ['message' => 'Git Repository is empty.']);
        }
        reply(200, ['ref' => "refs/heads/$branch", 'object' => ['sha' => $h, 'type' => 'commit']]);
    }

    // PUT /contents/<file>  (only used to create the first commit)
    if ($method === 'PUT' && preg_match('#^/contents/(.+)$#', $path, $mm)) {
        $b = body();
        $blob = blobFromBytes(base64_decode($b['content']));
        $idx = tempnam(sys_get_temp_dir(), 'idx');
        @unlink($idx);
        $env = ['GIT_INDEX_FILE' => $idx];
        $h = head();
        if ($h) {
            git(['read-tree', $h], null, $env);
        }
        git(['update-index', '--add', '--cacheinfo', "100644,$blob," . rawurldecode($mm[1])], null, $env);
        $tree = git(['write-tree'], null, $env);
        @unlink($idx);
        $who = ['GIT_AUTHOR_NAME' => $b['committer']['name'] ?? 'x', 'GIT_AUTHOR_EMAIL' => $b['committer']['email'] ?? 'x',
            'GIT_COMMITTER_NAME' => $b['committer']['name'] ?? 'x', 'GIT_COMMITTER_EMAIL' => $b['committer']['email'] ?? 'x'];
        $args = ['commit-tree', $tree, '-m', $b['message']];
        if ($h) {
            array_push($args, '-p', $h);
        }
        $c = git($args, null, $who);
        git(['update-ref', "refs/heads/$branch", $c]);
        reply(201, ['commit' => ['sha' => $c]]);
    }

    if (head() === null && str_starts_with($path, '/git/')) {
        reply(409, ['message' => 'Git Repository is empty.']);
    }

    // POST /git/blobs  ($MOCK_DIR/delay = seconds to stall, to keep a run busy)
    // Real GitHub refuses a blob request over ~56-63 MB (measured: 40 MiB of
    // data = 55.9 MB request accepted, 45 MiB = 62.9 MB refused).
    if ($method === 'POST' && $path === '/git/blobs' && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 58000000) {
        reply(422, ['message' => 'Sorry, your input was too large to process. Consider creating the blob in a local clone of the repository and then pushing it to GitHub.']);
    }
    if ($method === 'POST' && $path === '/git/blobs') {
        if (is_file("$dir/delay")) {
            sleep((int) file_get_contents("$dir/delay"));
        }
        $b = body();
        $bytes = ($b['encoding'] ?? 'utf-8') === 'base64' ? base64_decode($b['content'], true) : $b['content'];
        if ($bytes === false) {
            reply(422, ['message' => 'invalid base64']);
        }
        reply(201, ['sha' => blobFromBytes($bytes)]);
    }

    // GET /git/blobs/<sha>  (raw)
    if ($method === 'GET' && preg_match('#^/git/blobs/([0-9a-f]{40})$#', $path, $mm)) {
        reply(200, null, '');
        header('Content-Type: application/octet-stream');
        $p = proc_open(['git', '--git-dir=' . $repo, 'cat-file', 'blob', $mm[1]], [1 => ['pipe', 'w']], $pipes);
        while (! feof($pipes[1])) {
            echo fread($pipes[1], 1 << 20);
            flush();
        }
        proc_close($p);
        exit;
    }

    // GET /git/commits/<sha>
    if ($method === 'GET' && preg_match('#^/git/commits/([0-9a-f]{40})$#', $path, $mm)) {
        $tree = git(['rev-parse', $mm[1] . '^{tree}']);
        reply(200, ['sha' => $mm[1], 'tree' => ['sha' => $tree]]);
    }

    // GET /git/trees/<sha>?recursive=1
    if ($method === 'GET' && preg_match('#^/git/trees/([0-9a-f]{40})$#', $path, $mm)) {
        $out = git(['ls-tree', '-r', '-t', '-l', '--full-tree', $mm[1]]);
        $entries = [];
        foreach (array_filter(explode("\n", $out)) as $line) {
            [$meta, $p] = explode("\t", $line, 2);
            [$mode, $type, $sha, $size] = preg_split('/\s+/', $meta);
            $e = ['path' => $p, 'mode' => $mode, 'type' => $type, 'sha' => $sha];
            if ($type === 'blob') {
                $e['size'] = (int) $size;
            }
            $entries[] = $e;
        }
        reply(200, ['sha' => $mm[1], 'tree' => $entries, 'truncated' => false]);
    }

    // POST /git/trees
    if ($method === 'POST' && $path === '/git/trees') {
        $b = body();
        $idx = tempnam(sys_get_temp_dir(), 'idx');
        @unlink($idx);
        $env = ['GIT_INDEX_FILE' => $idx];
        if (! empty($b['base_tree'])) {
            git(['read-tree', $b['base_tree']], null, $env);
        }
        foreach ($b['tree'] as $e) {
            if ($e['sha'] === null) {
                git(['update-index', '--force-remove', $e['path']], null, $env);
            } else {
                git(['update-index', '--add', '--cacheinfo', "{$e['mode']},{$e['sha']},{$e['path']}"], null, $env);
            }
        }
        $tree = git(['write-tree'], null, $env);
        @unlink($idx);
        reply(201, ['sha' => $tree]);
    }

    // POST /git/commits
    if ($method === 'POST' && $path === '/git/commits') {
        $b = body();
        $env = [
            'GIT_AUTHOR_NAME' => $b['author']['name'], 'GIT_AUTHOR_EMAIL' => $b['author']['email'],
            'GIT_COMMITTER_NAME' => $b['committer']['name'] ?? $b['author']['name'],
            'GIT_COMMITTER_EMAIL' => $b['committer']['email'] ?? $b['author']['email'],
        ];
        $args = ['commit-tree', $b['tree'], '-m', $b['message']];
        foreach ($b['parents'] as $p) {
            array_push($args, '-p', $p);
        }
        reply(201, ['sha' => git($args, null, $env)]);
    }

    // PATCH /git/refs/heads/main
    if ($method === 'PATCH' && $path === "/git/refs/heads/$branch") {
        $b = body();
        $cur = head();
        if (empty($b['force']) && $cur) {
            try {
                git(['merge-base', '--is-ancestor', $cur, $b['sha']]);
            } catch (RuntimeException $e) {
                reply(422, ['message' => 'Update is not a fast forward']);
            }
        }
        git(['update-ref', "refs/heads/$branch", $b['sha']]);
        reply(200, ['object' => ['sha' => $b['sha']]]);
    }

    // --- releases ----------------------------------------------------------
    $relFile = "$dir/releases.json";
    $loadRel = fn () => is_file($relFile) ? json_decode(file_get_contents($relFile), true) : ['next' => 1, 'releases' => []];
    $saveRel = fn ($st) => file_put_contents($relFile, json_encode($st), LOCK_EX);
    $view = function ($r) { $r['assets'] = array_values($r['assets']); return $r; };

    if ($method === 'GET' && preg_match('#^/releases/tags/(.+)$#', $path, $mm)) {
        foreach ($loadRel()['releases'] as $r) {
            if ($r['tag_name'] === rawurldecode($mm[1])) {
                reply(200, $view($r));
            }
        }
        reply(404, ['message' => 'Not Found']);
    }
    if ($method === 'GET' && $path === '/releases') {
        $all = array_reverse(array_values($loadRel()['releases']));
        $per = (int) ($_GET['per_page'] ?? 30);
        $page = (int) ($_GET['page'] ?? 1);
        reply(200, array_map($view, array_slice($all, ($page - 1) * $per, $per)));
    }
    if ($method === 'POST' && $path === '/releases') {
        $b = body();
        $st = $loadRel();
        foreach ($st['releases'] as $r) {
            if ($r['tag_name'] === $b['tag_name']) {
                reply(422, ['message' => 'Validation Failed', 'errors' => [['code' => 'already_exists', 'field' => 'tag_name']]]);
            }
        }
        $target = git(['rev-parse', $b['target_commitish'] ?? 'main']);
        git(['update-ref', 'refs/tags/' . $b['tag_name'], $target]);
        $id = $st['next']++;
        $st['releases'][$id] = ['id' => $id, 'tag_name' => $b['tag_name'], 'name' => $b['name'] ?? $b['tag_name'], 'assets' => []];
        $saveRel($st);
        reply(201, $view($st['releases'][$id]));
    }
    if ($method === 'GET' && preg_match('#^/releases/(\d+)$#', $path, $mm)) {
        $st = $loadRel();
        isset($st['releases'][$mm[1]]) ? reply(200, $view($st['releases'][$mm[1]])) : reply(404, ['message' => 'Not Found']);
    }
    if ($method === 'DELETE' && preg_match('#^/releases/(\d+)$#', $path, $mm)) {
        $st = $loadRel();
        if (! isset($st['releases'][$mm[1]])) {
            reply(404, ['message' => 'Not Found']);
        }
        foreach ($st['releases'][$mm[1]]['assets'] as $a) {
            @unlink("$dir/assets/{$a['id']}");
        }
        unset($st['releases'][$mm[1]]);
        $saveRel($st);
        reply(204, null, '');
        exit;
    }
    if ($method === 'POST' && preg_match('#^/releases/(\d+)/assets$#', $path, $mm)) {
        $name = $_GET['name'] ?? '';
        $st = $loadRel();
        if (! isset($st['releases'][$mm[1]])) {
            reply(404, ['message' => 'Not Found']);
        }
        foreach ($st['releases'][$mm[1]]['assets'] as $a) {
            if ($a['name'] === $name) {
                reply(422, ['message' => 'Validation Failed', 'errors' => [['code' => 'already_exists', 'field' => 'name']]]);
            }
        }
        @mkdir("$dir/assets");
        $id = $st['next']++;
        $in = fopen('php://input', 'rb');
        $out = fopen("$dir/assets/$id", 'wb');
        stream_copy_to_stream($in, $out);
        fclose($out);
        $st = $loadRel(); // re-read: the upload may have taken a while
        $st['next'] = max($st['next'], $id + 1);
        $st['releases'][$mm[1]]['assets'][$id] = ['id' => $id, 'name' => $name, 'size' => filesize("$dir/assets/$id"), 'state' => 'uploaded'];
        $saveRel($st);
        reply(201, $st['releases'][$mm[1]]['assets'][$id]);
    }
    if (preg_match('#^/releases/assets/(\d+)$#', $path, $mm)) {
        $st = $loadRel();
        $rid = null;
        foreach ($st['releases'] as $k => $r) {
            if (isset($r['assets'][$mm[1]])) {
                $rid = $k;
            }
        }
        if ($rid === null) {
            reply(404, ['message' => 'Not Found']);
        }
        if ($method === 'GET') {
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/octet-stream')) {
                file_put_contents("$dir/requests.log", "GET $uri 302\n", FILE_APPEND | LOCK_EX);
                header('Location: http://localhost:9999/_storage/' . $mm[1], true, 302);
                exit;
            }
            reply(200, $st['releases'][$rid]['assets'][$mm[1]]);
        }
        if ($method === 'PATCH') {
            $b = body();
            foreach ($st['releases'][$rid]['assets'] as $a) {
                if ($a['name'] === $b['name'] && $a['id'] != $mm[1]) {
                    reply(422, ['message' => 'Validation Failed', 'errors' => [['code' => 'already_exists', 'field' => 'name']]]);
                }
            }
            $st['releases'][$rid]['assets'][$mm[1]]['name'] = $b['name'];
            $saveRel($st);
            reply(200, $st['releases'][$rid]['assets'][$mm[1]]);
        }
        if ($method === 'DELETE') {
            @unlink("$dir/assets/{$mm[1]}");
            unset($st['releases'][$rid]['assets'][$mm[1]]);
            $saveRel($st);
            reply(204, null, '');
            exit;
        }
    }
    if ($method === 'DELETE' && preg_match('#^/git/refs/tags/(.+)$#', $path, $mm)) {
        try {
            git(['update-ref', '-d', 'refs/tags/' . rawurldecode($mm[1])]);
        } catch (RuntimeException $e) {
            reply(422, ['message' => 'Reference does not exist']);
        }
        reply(204, null, '');
        exit;
    }

    reply(404, ['message' => 'Not Found']);
} catch (RuntimeException $e) {
    reply(500, ['message' => $e->getMessage()]);
}
