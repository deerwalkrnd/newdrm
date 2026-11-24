<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Checks the HMAC on requests from the GitHub Actions trigger:
 *
 *   X-Backup-Timestamp: <unix seconds>
 *   X-Backup-Signature: sha256=<hex HMAC-SHA256(secret, "<timestamp>.<raw body>")>
 *
 * Refuses timestamps more than 5 minutes off, and any signature already
 * seen (replay), remembered for 10 minutes.
 */
class VerifyBackupSignature
{
    public function handle(Request $request, Closure $next)
    {
        $secret = (string) config('dump-github.trigger.secret');
        if (strlen($secret) < 32) {
            return response()->json(['error' => 'trigger disabled'], 503);
        }

        $ts = (string) $request->header('X-Backup-Timestamp', '');
        $sig = (string) $request->header('X-Backup-Signature', '');
        $skew = (int) config('dump-github.trigger.max_skew_seconds', 300);

        if (! ctype_digit($ts) || abs(time() - (int) $ts) > $skew) {
            return $this->deny($request, 'stale or missing timestamp');
        }
        $want = 'sha256=' . hash_hmac('sha256', $ts . '.' . $request->getContent(), $secret);
        if (! hash_equals($want, $sig)) {
            return $this->deny($request, 'bad signature');
        }
        if (! Cache::add('backup-trigger-sig:' . hash('sha256', $sig), 1, 2 * $skew)) {
            return $this->deny($request, 'replayed signature');
        }

        return $next($request);
    }

    private function deny(Request $request, string $why)
    {
        Log::warning("backup trigger refused from {$request->ip()}: $why");

        return response()->json(['error' => 'unauthorized'], 401);
    }
}
