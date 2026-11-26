<?php

namespace App\Services\Backup;

use Carbon\CarbonImmutable;

/**
 * Naming, slot and retention rules for the dumps repo.
 *
 *   backups/MM-DD/<db>-HH0000.sql.gz            (or .partNNN when split)
 *   backups/MM-DD/<db>-HH0000.sql.gz.sha256
 *   backups/MM-DD/<db>_files-HH0000.tar.gz      (Sunday 06:00 only)
 *
 * A "stem" is the logical file (backups/MM-DD/<db>-HH0000.sql.gz); its
 * members are the stem itself or its parts, plus the .sha256.
 */
class BackupPaths
{
    public function __construct(
        private string $root,      // "backups"
        private string $dbName,
        private array $slots,      // [6, 18]
        private string $timezone,  // Asia/Kathmandu
    ) {
        if (! preg_match('/^[A-Za-z0-9_]+$/', $dbName)) {
            throw new BackupException("database name '$dbName' is not safe to use in a file name");
        }
        sort($this->slots);
    }

    public static function fromConfig(): self
    {
        $c = config('dump-github');
        $conn = $c['database']['connection'];

        return new self($c['path'], (string) config("database.connections.$conn.database"), $c['slots'], $c['timezone']);
    }

    /**
     * The slot a run at $now belongs to: the most recent 06:00 or 18:00
     * that has started. 02:00 belongs to yesterday's 18:00.
     */
    public function slotFor(CarbonImmutable $now): CarbonImmutable
    {
        $local = $now->setTimezone($this->timezone);
        foreach (array_reverse($this->slots) as $h) {
            $start = $local->setTime($h, 0);
            if ($local >= $start) {
                return $start;
            }
        }

        return $local->subDay()->setTime(end($this->slots), 0);
    }

    public function folder(CarbonImmutable $slot): string
    {
        return $this->root . '/' . $slot->format('m-d');
    }

    public function dbStem(CarbonImmutable $slot): string
    {
        return $this->folder($slot) . '/' . $this->dbName . '-' . $slot->format('H') . '0000.sql.gz';
    }

    public function filesStem(CarbonImmutable $slot): string
    {
        return $this->folder($slot) . '/' . $this->dbName . '_files-' . $slot->format('H') . '0000.tar.gz';
    }

    private function pattern(): string
    {
        $root = preg_quote($this->root, '#');
        $db = preg_quote($this->dbName, '#');

        return '#^(?<stem>' . $root . '/(?<md>\d\d-\d\d)/(?:(?<db>' . $db . ')-(?<h1>\d\d)0000\.sql\.gz|(?<files>' . $db . '_files)-(?<h2>\d\d)0000\.tar\.gz))'
            . '(?<suffix>\.part\d{3}|\.sha256)?$#';
    }

    /** @return array{stem: string, md: string, hour: int, kind: string, suffix: string}|null */
    public function parse(string $path): ?array
    {
        if (! preg_match($this->pattern(), $path, $m)) {
            return null;
        }
        $hour = (int) (($m['h1'] ?? '') !== '' ? $m['h1'] : $m['h2']);
        if (! in_array($hour, $this->slots, true)) {
            return null;
        }
        [$mm, $dd] = array_map('intval', explode('-', $m['md']));
        if ($mm < 1 || $mm > 12 || $dd < 1 || $dd > 31) {
            return null;
        }

        return [
            'stem' => $m['stem'],
            'md' => $m['md'],
            'hour' => $hour,
            'kind' => ($m['files'] ?? '') !== '' ? 'files' : 'db',
            'suffix' => $m['suffix'] ?? '',
        ];
    }

    public function isArtefact(string $path): bool
    {
        return $this->parse($path) !== null;
    }

    /**
     * Folder names carry no year. A month-day after today belongs to an
     * earlier year (on 11-19, "12-25" is last December). 02-29 walks back
     * to the last leap year.
     */
    public function resolveDate(string $md, CarbonImmutable $today): ?CarbonImmutable
    {
        [$mm, $dd] = array_map('intval', explode('-', $md));
        $today = $today->setTimezone($this->timezone)->startOfDay();
        for ($y = (int) $today->format('Y'), $i = 0; $i < 8; $i++, $y--) {
            if (! checkdate($mm, $dd, $y)) {
                continue;
            }
            $d = CarbonImmutable::create($y, $mm, $dd, 0, 0, 0, $this->timezone);
            if ($d <= $today) {
                return $d;
            }
        }

        return null;
    }

    /** Day 0 is today; with 30 days, day 30 is kept and day 31 is expired. */
    public function isExpired(string $md, CarbonImmutable $today, int $retentionDays): bool
    {
        $d = $this->resolveDate($md, $today);
        if ($d === null) {
            return false;
        }
        $age = (int) $d->diffInDays($today->setTimezone($this->timezone)->startOfDay());

        return $age > $retentionDays;
    }

    /**
     * Group repo blobs into logical backups.
     *
     * @param  array<string, array{sha: string, size: int}>  $blobs
     * @return array<string, array{stem: string, md: string, hour: int, kind: string, members: array<string, array{sha: string, size: int}>}>
     */
    public function group(array $blobs): array
    {
        $out = [];
        foreach ($blobs as $path => $info) {
            $p = $this->parse($path);
            if ($p === null) {
                continue;
            }
            $out[$p['stem']] ??= ['stem' => $p['stem'], 'md' => $p['md'], 'hour' => $p['hour'], 'kind' => $p['kind'], 'members' => []];
            $out[$p['stem']]['members'][$path] = $info;
        }
        foreach ($out as &$g) {
            ksort($g['members']);
        }

        return $out;
    }
}
