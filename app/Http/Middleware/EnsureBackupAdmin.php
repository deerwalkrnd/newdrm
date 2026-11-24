<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * The Backups page holds the GitHub token and can download full dumps, so
 * only HR accounts whose username is in dump-github.admins (default: the
 * DMT account, "dmt") may open it. Everyone else gets a 404: the page is
 * not advertised. Use after "logged-in".
 */
class EnsureBackupAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! self::allows($request->user())) {
            abort(404);
        }

        return $next($request);
    }

    public static function allows($user): bool
    {
        if (! $user || ($user->role->authority ?? null) !== 'hr') {
            return false;
        }
        $admins = array_map('strtolower', (array) config('dump-github.admins', []));

        return in_array(strtolower((string) $user->username), $admins, true);
    }
}
