<?php

namespace App\Services\Backup;

/** Another backup run holds the lock. Not a failure: no /fail ping. */
class BackupLockedException extends BackupException
{
}
