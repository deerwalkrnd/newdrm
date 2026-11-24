<?php

namespace App\Services\Backup;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/** healthchecks.io pings. Never throws: a dead monitor must not break a backup. */
class Healthcheck
{
    public function __construct(private ?string $url, private ?Client $http = null)
    {
        $this->url = $url ? rtrim($url, '/') : null;
    }

    public function start(): void
    {
        $this->ping('/start', '');
    }

    public function success(string $body): void
    {
        $this->ping('', $body);
    }

    public function fail(string $body): void
    {
        $this->ping('/fail', $body);
    }

    private function ping(string $suffix, string $body): void
    {
        if (! $this->url) {
            return;
        }
        try {
            ($this->http ?? new Client(['timeout' => 10, 'connect_timeout' => 5]))
                ->post($this->url . $suffix, ['body' => substr($body, 0, 10000)]);
        } catch (\Throwable $e) {
            Log::warning('backup: healthcheck ping failed: ' . $e->getMessage());
        }
    }
}
