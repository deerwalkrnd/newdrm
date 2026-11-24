<?php

namespace Tests\Feature\Backup;

use App\Models\User;
use App\Services\Backup\BackupSettings;
use App\Services\Backup\DownloadLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Needs the database. The download test also needs the mock GitHub with a
 * backup in it, so it only runs from tests/backup-e2e (BACKUP_E2E=1).
 */
class DatabaseBackupPageTest extends TestCase
{
    use DatabaseTransactions;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/backup-page-test-' . bin2hex(random_bytes(4));
        mkdir($this->dir);
        config([
            'dump-github.settings_file' => $this->dir . '/settings.json',
            'dump-github.download_log' => $this->dir . '/downloads.log',
        ]);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir . '/*') ?: []);
        @rmdir($this->dir);
        parent::tearDown();
    }

    /** Seeded by tests/backup-e2e/seed.sql: dmt (HR, backup admin), hr2 (HR), emp3 (employee). */
    private function user(string $username): User
    {
        $u = User::where('username', $username)->first();
        if (! $u) {
            $this->markTestSkipped('needs the e2e seed data (tests/backup-e2e)');
        }

        return $u;
    }

    public function test_other_hr_and_employees_get_404_and_no_menu_link()
    {
        foreach (['hr2', 'emp3'] as $name) {
            $u = $this->user($name);
            $this->actingAs($u)->get('/database-backups')->assertStatus(404);
            $this->actingAs($u)->get('/database-backups/latest/download')->assertStatus(404);
            $this->actingAs($u)->post('/database-backups/run')->assertStatus(404);
            $this->actingAs($u)->post('/database-backups/settings', ['github_token' => 'x'])->assertStatus(404);
        }
        $this->assertFileDoesNotExist($this->dir . '/settings.json');
        $this->assertFalse(\App\Http\Middleware\EnsureBackupAdmin::allows($this->user('hr2')));
        $this->assertTrue(\App\Http\Middleware\EnsureBackupAdmin::allows($this->user('dmt')));
    }

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/database-backups')->assertRedirect('/login');
    }

    public function test_backup_admin_saves_settings_encrypted_and_blank_keeps_them()
    {
        config(['dump-github.github.api_url' => 'http://127.0.0.1:1']); // GitHub check fails fast
        $u = $this->user('dmt');
        $token = 'github_pat_TESTTOKEN1234567890';
        $secret = str_repeat('s', 40);

        $this->actingAs($u)->post('/database-backups/settings', [
            'github_token' => $token, 'trigger_secret' => $secret,
            'healthcheck_url' => 'https://hc-ping.com/abc', 'enabled' => '1',
        ])->assertRedirect(route('backup.index'));

        $raw = file_get_contents($this->dir . '/settings.json');
        $this->assertStringNotContainsString($token, $raw, 'stored encrypted');
        $this->assertStringNotContainsString($secret, $raw);
        $this->assertSame($token, config('dump-github.github.token'));
        $this->assertTrue(config('dump-github.enabled'));

        // A fresh boot reads them back; blank fields keep the stored values.
        config(['dump-github.github.token' => null, 'dump-github.trigger.secret' => null]);
        BackupSettings::applyToConfig();
        $this->assertSame($token, config('dump-github.github.token'));
        $this->actingAs($u)->post('/database-backups/settings', ['github_token' => '', 'trigger_secret' => '', 'healthcheck_url' => '']);
        $this->assertSame($token, config('dump-github.github.token'));
        $this->assertSame($secret, config('dump-github.trigger.secret'));
        $this->assertFalse(config('dump-github.enabled'), 'unticked = off');

        $this->actingAs($u)->get('/database-backups')->assertOk()
            ->assertSee('…7890')->assertDontSee($token)->assertDontSee($secret);
    }

    public function test_short_trigger_secret_is_rejected()
    {
        $u = $this->user('dmt');
        $this->actingAs($u)
            ->post('/database-backups/settings', ['trigger_secret' => 'short'])
            ->assertSessionHasErrors('trigger_secret');
        $this->assertFileDoesNotExist($this->dir . '/settings.json');
    }

    public function test_backup_admin_downloads_latest_and_it_is_logged()
    {
        if (! getenv('BACKUP_E2E')) {
            $this->markTestSkipped('needs the mock GitHub (tests/backup-e2e)');
        }
        $u = $this->user('dmt');
        // The e2e backups are for fake 2026-11-19 slots, days before "now".
        $this->actingAs($u)->get('/database-backups')->assertOk()
            ->assertSee('newdrm-')->assertSee('Backups are overdue');

        $res = $this->actingAs($u)->get('/database-backups/latest/download');
        $res->assertOk();
        // The stream calls ob_flush(), so collect in the buffer's callback.
        $body = '';
        ob_start(function ($chunk) use (&$body) {
            $body .= $chunk;

            return '';
        });
        $res->baseResponse->sendContent();
        ob_end_clean();
        $this->assertSame("\x1f\x8b", substr($body, 0, 2), 'gzip data');
        file_put_contents('/work/http-download.gz', $body);

        $log = DownloadLog::latest(1);
        $this->assertSame($u->id, $log[0]['user_id']);
        $this->assertStringEndsWith('newdrm-060000.sql.gz', $log[0]['path']);
        $this->actingAs($u)->get('/database-backups')->assertSee($log[0]['path']);
    }
}
