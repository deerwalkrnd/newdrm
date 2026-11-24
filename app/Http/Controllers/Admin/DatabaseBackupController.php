<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Backup\BackupCatalog;
use App\Services\Backup\BackupException;
use App\Services\Backup\BackupSettings;
use App\Services\Backup\DownloadLog;
use App\Services\Backup\GitHubClient;
use App\Services\Backup\RunAfterResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** Backup admins only (EnsureBackupAdmin, routes/web.php): settings, list, download, run now. */
class DatabaseBackupController extends Controller
{
    public function index()
    {
        $backups = [];
        $error = null;
        if (config('dump-github.github.token')) {
            try {
                $backups = BackupCatalog::fromConfig()->list();
            } catch (BackupException $e) {
                $error = $e->getMessage();
            }
        }
        $token = (string) config('dump-github.github.token');
        $secret = (string) config('dump-github.trigger.secret');

        // Age of the newest complete database backup, for the "overdue"
        // warning (a slot every 12 hours; warn after 14).
        $last = null;
        foreach ($backups as $b) {
            if ($b['kind'] === 'db' && $b['complete'] && $b['date']) {
                $last = $b['date'];
                break;
            }
        }
        $overdue = $token !== '' && $error === null && config('dump-github.enabled')
            && ($last === null || $last->diffInHours(now()) >= 14);

        return view('admin.backup.index', [
            'backups' => $backups,
            'error' => $error,
            'repo' => config('dump-github.github.owner') . '/' . config('dump-github.github.repo'),
            'downloads' => DownloadLog::latest(20),
            'settings' => [
                'enabled' => (bool) config('dump-github.enabled'),
                'token' => $token === '' ? null : '…' . substr($token, -4),
                'secret' => $secret === '' ? null : (strlen($secret) >= 32 ? 'set' : 'too short'),
                'healthcheck_url' => (string) config('dump-github.healthcheck_url'),
            ],
            'triggerUrl' => url('/api/backup/trigger'),
            'lastBackup' => $last,
            'overdue' => $overdue,
        ]);
    }

    /**
     * Save settings (encrypted with APP_KEY, see BackupSettings). Blank token
     * or secret fields keep the stored value. Then check the token works.
     */
    public function settings(Request $request)
    {
        $data = $request->validate([
            'github_token' => ['nullable', 'string', 'max:255'],
            'trigger_secret' => ['nullable', 'string', 'min:32', 'max:255'],
            'healthcheck_url' => ['nullable', 'url', 'max:255'],
        ]);
        BackupSettings::save([
            'enabled' => $request->boolean('enabled'),
            'github_token' => $data['github_token'] ?? null,       // null = keep
            'trigger_secret' => $data['trigger_secret'] ?? null,
            'healthcheck_url' => (string) ($data['healthcheck_url'] ?? ''),
        ]);
        Log::notice('backup: settings changed by user ' . $request->user()->id);

        $msg = 'Settings saved.';
        try {
            $gh = GitHubClient::fromConfig();
            $head = $gh->headCommit();
            $msg .= ' GitHub access OK (' . $gh->repoName() . ($head ? '' : ', empty repo') . ').';
            $type = 'status';
        } catch (BackupException $e) {
            $msg .= ' But GitHub check failed: ' . $e->getMessage();
            $type = 'warning';
        }

        return redirect()->route('backup.index')->with($type, $msg);
    }

    /** The newest complete database backup. */
    public function latest(Request $request)
    {
        $catalog = BackupCatalog::fromConfig();
        $backup = $catalog->latest('db');
        abort_if($backup === null, 404, 'No backup found');

        return $this->send($request, $catalog, $backup);
    }

    public function download(Request $request)
    {
        $stem = (string) $request->query('stem');
        $catalog = BackupCatalog::fromConfig();
        $backup = $catalog->find($stem);
        abort_if($backup === null || ! $backup['complete'], 404, 'No such backup');

        return $this->send($request, $catalog, $backup);
    }

    public function run(Request $request)
    {
        abort_unless(config('dump-github.enabled'), 503, 'Backups are switched off (see Settings)');
        Log::info('backup: run now requested by user ' . $request->user()->id);
        RunAfterResponse::backup($request->boolean('with_files') ? true : null, false, 'manual:user' . $request->user()->id);

        return redirect()->route('backup.index')
            ->with('status', 'Backup started. It takes a minute or two; reload this page to see it.');
    }

    /** Streams the (joined) .gz from GitHub. The server never stores it. */
    private function send(Request $request, BackupCatalog $catalog, array $backup)
    {
        $user = $request->user();
        DownloadLog::record([
            'user_id' => $user->id,
            'user' => $user->username . (optional($user->employee)->email ? ' <' . $user->employee->email . '>' : ''),
            'path' => $backup['stem'],
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
        Log::notice("backup: {$backup['stem']} downloaded by user {$user->id} from {$request->ip()}");

        return response()->streamDownload(function () use ($catalog, $backup) {
            @set_time_limit(0);
            $catalog->stream($backup);
        }, $backup['name'], [
            'Content-Type' => 'application/gzip',
            'Content-Length' => (string) $backup['size'],
            'Cache-Control' => 'no-store',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
