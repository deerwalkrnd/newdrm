<?php

namespace App\Services\Backup;

/**
 * flock() on a file in storage/. The kernel releases it when the process
 * exits, so a run killed half-way never leaves a stale lock behind.
 */
class BackupLock
{
    /** @var resource|null */
    private $fh = null;

    public function __construct(private string $file)
    {
    }

    public function acquire(): void
    {
        $fh = fopen($this->file, 'c');
        if (! $fh) {
            throw new BackupException("cannot open lock file {$this->file}");
        }
        if (! flock($fh, LOCK_EX | LOCK_NB)) {
            fclose($fh);
            throw new BackupLockedException('another backup run is in progress');
        }
        ftruncate($fh, 0);
        fwrite($fh, getmypid() . ' ' . date('c') . "\n");
        fflush($fh);
        $this->fh = $fh;
    }

    public function release(): void
    {
        if ($this->fh) {
            flock($this->fh, LOCK_UN);
            fclose($this->fh);
            $this->fh = null;
        }
    }

    public function __destruct()
    {
        $this->release();
    }
}
