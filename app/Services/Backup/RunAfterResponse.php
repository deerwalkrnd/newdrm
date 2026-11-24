<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\Log;

/**
 * Run a backup after the HTTP response has been sent.
 *
 * QUEUE_CONNECTION is sync and no worker runs, so the work happens in this
 * same PHP process, in Laravel's terminating phase. Symfony's
 * Response::send() calls fastcgi_finish_request() (PHP-FPM) or
 * litespeed_finish_request(), so the client already has its 202 and the
 * web timeout can't kill the run. ignore_user_abort() covers mod_php.
 */
class RunAfterResponse
{
    public static function backup(?bool $withFiles, bool $ifDue, string $trigger): void
    {
        app()->terminating(function () use ($withFiles, $ifDue, $trigger) {
            ignore_user_abort(true);
            @set_time_limit(0);
            try {
                $r = BackupService::fromConfig()->run($withFiles, $ifDue, null, $trigger);
                Log::info("backup ($trigger): {$r['status']} {$r['slot']}");
            } catch (BackupLockedException $e) {
                Log::info("backup ($trigger): skipped, " . $e->getMessage());
            } catch (\Throwable $e) {
                // Already logged and /fail pinged by BackupService.
            }
        });
    }
}
