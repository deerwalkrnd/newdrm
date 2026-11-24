<?php

namespace App\Services\Backup;

/**
 * Who downloaded which backup, as JSON lines in storage/logs (no table, so
 * no migration). The Backups page shows the newest entries.
 */
class DownloadLog
{
    public static function path(): string
    {
        return (string) config('dump-github.download_log');
    }

    public static function record(array $entry): void
    {
        $entry = ['at' => now()->toIso8601String()] + $entry;
        if (! is_dir(dirname(self::path()))) {
            mkdir(dirname(self::path()), 0755, true);
        }
        file_put_contents(self::path(), json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND | LOCK_EX);
    }

    /** @return array<int, array<string, mixed>> newest first */
    public static function latest(int $n = 20): array
    {
        if (! is_file(self::path())) {
            return [];
        }
        $lines = array_slice(file(self::path(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), -$n);

        return array_reverse(array_values(array_filter(array_map(fn ($l) => json_decode($l, true), $lines))));
    }
}
