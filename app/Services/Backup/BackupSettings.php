<?php

namespace App\Services\Backup;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Backup settings a backup admin enters on the Backups page, so the GitHub
 * token and trigger secret never have to be put in the server's .env.
 *
 * Stored in storage/app/backup-settings.json, encrypted with APP_KEY.
 * Non-empty values override the DUMP_* .env settings; .env still works.
 */
class BackupSettings
{
    /** setting => config key */
    public const FIELDS = [
        'enabled' => 'dump-github.enabled',
        'github_token' => 'dump-github.github.token',
        'trigger_secret' => 'dump-github.trigger.secret',
        'healthcheck_url' => 'dump-github.healthcheck_url',
    ];

    public static function path(): string
    {
        return (string) config('dump-github.settings_file');
    }

    /** @return array<string, mixed> */
    public static function load(): array
    {
        $file = self::path();
        if (! is_file($file)) {
            return [];
        }
        try {
            $data = json_decode(Crypt::decryptString((string) file_get_contents($file)), true);
        } catch (DecryptException $e) {
            Log::warning('backup: cannot decrypt backup settings (APP_KEY changed?); enter them again on the Backups page');

            return [];
        }

        return is_array($data) ? array_intersect_key($data, self::FIELDS) : [];
    }

    /** Called from AppServiceProvider::boot(). */
    public static function applyToConfig(): void
    {
        foreach (self::load() as $key => $value) {
            if ($value !== null && $value !== '') {
                config([self::FIELDS[$key] => $value]);
            }
        }
    }

    /**
     * Merge and save. A null value keeps the stored one; '' clears it.
     *
     * @param  array<string, mixed>  $changes
     */
    public static function save(array $changes): void
    {
        $data = self::load();
        foreach (array_intersect_key($changes, self::FIELDS) as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        $file = self::path();
        if (! is_dir(dirname($file))) {
            mkdir(dirname($file), 0700, true);
        }
        $tmp = $file . '.tmp';
        file_put_contents($tmp, Crypt::encryptString(json_encode($data)), LOCK_EX);
        @chmod($tmp, 0600);
        rename($tmp, $file);
        self::applyToConfig();
    }
}
