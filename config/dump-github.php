<?php

/*
|--------------------------------------------------------------------------
| Database backups to a private GitHub "dumps" repo
|--------------------------------------------------------------------------
|
| The app dumps its own database and uploads it through the GitHub API:
| the dump file goes to that day's release, and only its .sha256 is
| committed. Nothing runs on the server by itself: a signed request from a
| GitHub Actions workflow, four times a day (or "Run now" on the Backups
| page), starts each run. See scripts/backup/README.md.
|
*/

return [

    // Master switch. When false, the trigger endpoint answers 503 and
    // backup:run refuses to start.
    //
    // enabled, the GitHub token, the trigger secret and the healthcheck URL
    // can instead be set on the Backups page (stored
    // encrypted with APP_KEY in settings_file). Those override .env.
    'enabled' => (bool) env('DUMP_ENABLED', false),
    'settings_file' => env('DUMP_SETTINGS_FILE', storage_path('app/backup-settings.json')),
    // Who may open the Backups page: HR accounts with one of these
    // usernames. Everyone else gets a 404 and sees no menu link.
    'admins' => array_values(array_filter(array_map('trim', explode(',', (string) env('DUMP_ADMIN_USERNAMES', 'dmt'))))),

    'download_log' => env('DUMP_DOWNLOAD_LOG', storage_path('logs/backup-downloads.log')),

    'github' => [
        // Fine-grained PAT: Contents read/write on the dumps repo only.
        'token' => env('DUMP_GITHUB_TOKEN'),
        'owner' => env('DUMP_GITHUB_OWNER', 'deerwalk-dumps'),
        'repo' => env('DUMP_GITHUB_REPO', 'DMR-dumps'),
        'branch' => env('DUMP_GITHUB_BRANCH', 'main'),
        // Only changed by the tests, which point it at a local fake.
        'api_url' => rtrim(env('DUMP_GITHUB_API_URL', 'https://api.github.com'), '/'),
        // Release asset uploads. Empty = derived (uploads.github.com for GitHub).
        'uploads_url' => env('DUMP_GITHUB_UPLOADS_URL', ''),
        'timeout' => (int) env('DUMP_GITHUB_TIMEOUT', 600),
    ],

    // Folder inside the dumps repo.
    'path' => trim(env('DUMP_GITHUB_PATH', 'backups'), '/'),

    'author' => [
        'name' => env('DUMP_GIT_AUTHOR_NAME', 'cronjob'),
        'email' => env('DUMP_GIT_AUTHOR_EMAIL', 'cronjob@localhost'),
    ],

    // Slots are 06:00 and 18:00 in this timezone. A run belongs to the
    // most recent slot that has started.
    'timezone' => env('DUMP_TIMEZONE', 'Asia/Kathmandu'),
    'slots' => [6, 18],

    // Backups older than this many days are pruned (day 30 kept, 31 removed).
    'retention_days' => (int) env('BACKUP_RETENTION_DAYS', 30),

    // Files larger than this are split into .part000, .part001, ...
    // Dump files are release assets (GitHub limit: 2 GiB each). Bigger files
    // are split; values above GitHubClient::MAX_ASSET_BYTES are clamped.
    'split_bytes' => (int) env('DUMP_SPLIT_BYTES', 1024 * 1024 * 1024),

    // Uploaded files go with the Sunday 06:00 slot (ISO day 7).
    'files' => [
        'enabled' => (bool) env('DUMP_FILES_ENABLED', true),
        'weekday' => 7,
        'slot' => 6,
        'root' => storage_path('app'),
        // Relative to root: the backup's own files.
        'exclude' => ['backup-tmp', 'backup.lock', 'backup-settings.json'],
    ],

    'database' => [
        'connection' => env('DUMP_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
        // Optional read-only user. Leave empty to use the app's DB user.
        'username' => env('DUMP_DB_USERNAME'),
        'password' => env('DUMP_DB_PASSWORD'),
        // auto: mysqldump if it can run, else the pure-PHP dumper.
        // mysqldump | php force one of them.
        'method' => env('DUMP_METHOD', 'auto'),
        'mysqldump_binary' => env('DUMP_MYSQLDUMP_BINARY', 'mysqldump'),
    ],

    // Temp directory for dump, gzip and split files. Always cleaned up.
    'tmp_dir' => env('DUMP_TMP_DIR', storage_path('app/backup-tmp')),

    // healthchecks.io ping URL (https://hc-ping.com/<uuid>). Empty = off.
    'healthcheck_url' => env('DUMP_HEALTHCHECK_URL'),

    'trigger' => [
        // Shared with the GitHub Actions workflow (BACKUP_TRIGGER_SECRET).
        // At least 32 random bytes. Empty = endpoint disabled.
        'secret' => env('DUMP_TRIGGER_SECRET'),
        'max_skew_seconds' => 300,
        'rate_per_hour' => (int) env('DUMP_TRIGGER_RATE_PER_HOUR', 12),
    ],

];
