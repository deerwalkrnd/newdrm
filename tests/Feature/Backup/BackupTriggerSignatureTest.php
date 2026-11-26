<?php

namespace Tests\Feature\Backup;

use Tests\TestCase;

class BackupTriggerSignatureTest extends TestCase
{
    private string $secret = '0123456789abcdef0123456789abcdef-test';

    protected function setUp(): void
    {
        parent::setUp();
        // Disabled: a request that passes the signature check gets 503, so
        // these tests never start a real backup.
        config(['dump-github.trigger.secret' => $this->secret, 'dump-github.enabled' => false]);
    }

    private function send(string $ts, string $sig, string $body = '{}')
    {
        return $this->call('POST', '/api/backup/trigger', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_BACKUP_TIMESTAMP' => $ts,
            'HTTP_X_BACKUP_SIGNATURE' => $sig,
            'REMOTE_ADDR' => '10.0.0.' . random_int(1, 250),
        ], $body);
    }

    private function sign(string $ts, string $body = '{}', ?string $secret = null): string
    {
        return 'sha256=' . hash_hmac('sha256', $ts . '.' . $body, $secret ?? $this->secret);
    }

    public function test_valid_signature_passes_once_and_replay_is_refused()
    {
        $ts = (string) time();
        $this->send($ts, $this->sign($ts))->assertStatus(503); // got past the signature check
        $this->send($ts, $this->sign($ts))->assertStatus(401); // replay
    }

    public function test_bad_wrong_secret_tampered_stale_and_future_are_refused()
    {
        $ts = (string) time();
        $this->send($ts, 'sha256=deadbeef')->assertStatus(401);
        $this->send($ts, $this->sign($ts, '{}', 'another-secret-another-secret-xx'))->assertStatus(401);
        $this->send($ts, $this->sign($ts, '{"x":1}'))->assertStatus(401); // body tampered
        $old = (string) (time() - 301);
        $this->send($old, $this->sign($old))->assertStatus(401);
        $future = (string) (time() + 301);
        $this->send($future, $this->sign($future))->assertStatus(401);
        $this->send('', $this->sign(''))->assertStatus(401);
    }

    public function test_short_or_missing_secret_disables_the_endpoint()
    {
        config(['dump-github.trigger.secret' => 'short']);
        $ts = (string) time();
        $this->send($ts, $this->sign($ts, '{}', 'short'))->assertStatus(503);
    }

    public function test_enabled_endpoint_returns_202()
    {
        // The test kernel runs terminating callbacks, so the after-response
        // run happens: without a token it fails at once (and pings nothing).
        config(['dump-github.enabled' => true, 'dump-github.github.token' => null, 'dump-github.healthcheck_url' => null]);
        $ts = (string) time();
        $this->send($ts, $this->sign($ts))->assertStatus(202)->assertJson(['status' => 'accepted']);
    }
}
