<?php

namespace App\Console\Commands;

use App\Services\Backup\BackupLockedException;
use App\Services\Backup\BackupService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class BackupRun extends Command
{
    protected $signature = 'backup:run
        {--with-files : also archive uploaded files (default: only on the Sunday 06:00 slot)}
        {--if-due : do nothing if this slot is already in the dumps repo}
        {--now= : (testing) pretend the current time is this}';

    protected $description = 'Dump the database and upload it to the private GitHub dumps repo';

    public function handle(): int
    {
        $now = $this->option('now') ? CarbonImmutable::parse($this->option('now')) : null;
        try {
            $r = BackupService::fromConfig()->run(
                $this->option('with-files') ? true : null,
                (bool) $this->option('if-due'),
                $now,
                'cli',
            );
        } catch (BackupLockedException $e) {
            $this->error('backup: ' . $e->getMessage());

            return 3;
        } catch (\Throwable $e) {
            $this->error('backup failed: ' . $e->getMessage());

            return 1;
        }
        $this->info("backup {$r['status']}: slot {$r['slot']}");
        foreach (array_merge($r['files'], $r['notes']) as $line) {
            $this->line("  $line");
        }

        return 0;
    }
}
