@extends('layouts.hr.app')

@section('title','Database backups')

@section('content')


<div class="container-fluid mt-3">
    <h3>Database backups</h3>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ implode(' ', $errors->all()) }}</div>
    @endif
    @if($error)
        <div class="alert alert-danger">Cannot list backups: {{ $error }}</div>
    @endif

    @if($overdue)
        <div class="alert alert-danger">
            <strong>Backups are overdue.</strong>
            @if($lastBackup)
                The newest database backup is from {{ $lastBackup->format('Y-m-d H:i') }}
                ({{ $lastBackup->diffForHumans() }}).
            @else
                There is no database backup yet.
            @endif
            Check the trigger workflow in {{ $repo }} (Actions tab) and
            <code>storage/logs/laravel.log</code>, then press Run now.
        </div>
    @elseif($lastBackup)
        <div class="alert alert-success">Last database backup: {{ $lastBackup->format('Y-m-d H:i') }} ({{ $lastBackup->diffForHumans() }}).</div>
    @endif

    <p>
        Backups run at 06:00 and 18:00 (Nepal time) and go to the private GitHub repo
        <strong>{{ $repo }}</strong>. Uploaded files are included on Sunday mornings.
        Every download is logged.
    </p>


    <details class="mb-4" {{ $settings['token'] && $settings['enabled'] ? '' : 'open' }}>
        <summary><strong>Settings</strong>
            ({{ $settings['enabled'] ? 'on' : 'off' }},
            token {{ $settings['token'] ?? 'not set' }},
            trigger secret {{ $settings['secret'] ?? 'not set' }})</summary>
        <form method="POST" action="{{ route('backup.settings') }}" class="mt-2" style="max-width: 640px" autocomplete="off">
            @csrf
            <p class="text-muted small">
                Stored on the server encrypted with the app key; never shown again after saving.
                Leave the token or secret blank to keep the current one.
            </p>
            <div class="mb-2">
                <label class="form-label">GitHub token (fine-grained, Contents read/write on {{ $repo }} only)</label>
                <input type="password" name="github_token" class="form-control" placeholder="{{ $settings['token'] ? 'unchanged ('.$settings['token'].')' : 'github_pat_...' }}">
            </div>
            <div class="mb-2">
                <label class="form-label">Trigger secret (same value as BACKUP_TRIGGER_SECRET in the dumps repo, 32+ characters)</label>
                <input type="password" name="trigger_secret" class="form-control" placeholder="{{ $settings['secret'] ? 'unchanged' : '' }}">
            </div>
            <div class="mb-2">
                <label class="form-label">healthchecks.io ping URL (optional)</label>
                <input type="url" name="healthcheck_url" class="form-control" value="{{ $settings['healthcheck_url'] }}" placeholder="https://hc-ping.com/...">
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" name="enabled" value="1" id="bk-enabled" class="form-check-input" {{ $settings['enabled'] ? 'checked' : '' }}>
                <label for="bk-enabled" class="form-check-label">Backups on</label>
            </div>
            <p class="small">Trigger URL for the dumps repo secret BACKUP_TRIGGER_URL: <code>{{ $triggerUrl }}</code></p>
            <button class="btn btn-secondary">Save settings</button>
        </form>
    </details>
    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('backup.latest') }}" class="btn btn-primary">Download latest database backup</a>
        <form method="POST" action="{{ route('backup.run') }}" class="d-flex align-items-center gap-2">
            @csrf
            <label class="form-check-label"><input type="checkbox" name="with_files" value="1" class="form-check-input"> include uploaded files</label>
            <button class="btn btn-success" onclick="return confirm('Start a backup now?')">Run now</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead>
                <tr class="table_title">
                    <th>Slot</th><th>File</th><th>Type</th><th>Size</th><th>Parts</th><th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($backups as $b)
                <tr>
                    <td>{{ $b['date'] ? $b['date']->format('Y-m-d H:i') : '?' }}</td>
                    <td>{{ $b['name'] }}</td>
                    <td>{{ $b['kind'] === 'db' ? 'Database' : 'Files' }}</td>
                    <td>{{ number_format($b['size'] / 1048576, 1) }} MB</td>
                    <td>{{ $b['parts'] }}</td>
                    <td>
                        @if($b['complete'])
                            <a href="{{ route('backup.download', ['stem' => $b['stem']]) }}">Download</a>
                        @else
                            incomplete
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No backups yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <h5 class="mt-4">Recent downloads</h5>
    <table class="table table-sm table-striped">
        <thead><tr class="table_title"><th>When</th><th>Who</th><th>File</th><th>IP</th></tr></thead>
        <tbody>
        @forelse($downloads as $d)
            <tr>
                <td>{{ $d['at'] ?? '' }}</td>
                <td>{{ $d['user'] ?? ('user #' . ($d['user_id'] ?? '?')) }}</td>
                <td>{{ $d['path'] ?? '' }}</td>
                <td>{{ $d['ip'] ?? '' }}</td>
            </tr>
        @empty
            <tr><td colspan="4">None.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
