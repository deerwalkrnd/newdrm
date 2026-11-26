<?php

namespace App\Http\Controllers;

use App\Services\Backup\RunAfterResponse;

/**
 * Called hourly by the GitHub Actions workflow in the dumps repo. Answers
 * 202 at once; the run happens after the response and does nothing if the
 * current slot is already backed up, so a late or repeated call is harmless.
 */
class BackupTriggerController extends Controller
{
    public function __invoke()
    {
        if (! config('dump-github.enabled')) {
            return response()->json(['error' => 'backups disabled'], 503);
        }
        RunAfterResponse::backup(null, true, 'github-actions');

        return response()->json(['status' => 'accepted'], 202);
    }
}
