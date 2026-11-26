<?php

namespace Tests\Unit\Backup;

use App\Services\Backup\BackupPaths;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class BackupPathsTest extends TestCase
{
    private function paths(): BackupPaths
    {
        return new BackupPaths('backups', 'newdrm', [6, 18], 'Asia/Kathmandu');
    }

    /** @dataProvider slots */
    public function test_slot_is_the_latest_started_nepal_slot(string $utc, string $expected)
    {
        $stem = $this->paths()->dbStem($this->paths()->slotFor(CarbonImmutable::parse($utc)));
        $this->assertSame($expected, $stem);
    }

    public static function slots(): array
    {
        return [
            // NPT = UTC+05:45
            '06:00 NPT exactly' => ['2026-11-19T00:15:00Z', 'backups/11-19/newdrm-060000.sql.gz'],
            '06:12 NPT' => ['2026-11-19T00:27:00Z', 'backups/11-19/newdrm-060000.sql.gz'],
            '05:59 NPT is yesterday 18:00' => ['2026-11-19T00:14:00Z', 'backups/11-18/newdrm-180000.sql.gz'],
            '17:59 NPT' => ['2026-11-19T12:14:00Z', 'backups/11-19/newdrm-060000.sql.gz'],
            '18:00 NPT' => ['2026-11-19T12:15:00Z', 'backups/11-19/newdrm-180000.sql.gz'],
            '23:30 NPT' => ['2026-11-19T17:45:00Z', 'backups/11-19/newdrm-180000.sql.gz'],
            '02:00 NPT on Jan 1 is Dec 31 18:00' => ['2026-12-31T20:15:00Z', 'backups/12-31/newdrm-180000.sql.gz'],
        ];
    }

    public function test_retention_keeps_day_30_and_prunes_day_31_and_wraps_the_year()
    {
        $p = $this->paths();
        $today = CarbonImmutable::parse('2026-11-19 10:00', 'Asia/Kathmandu');
        $this->assertFalse($p->isExpired('11-19', $today, 30));
        $this->assertFalse($p->isExpired('10-20', $today, 30), 'day 30 kept');
        $this->assertTrue($p->isExpired('10-19', $today, 30), 'day 31 pruned');
        $this->assertSame('2025-12-25', $p->resolveDate('12-25', $today)->format('Y-m-d'), '12-25 is last December');
        $this->assertTrue($p->isExpired('12-25', $today, 30));
        $this->assertSame('2025-11-20', $p->resolveDate('11-20', $today)->format('Y-m-d'));

        $jan = CarbonImmutable::parse('2027-01-10', 'Asia/Kathmandu');
        $this->assertFalse($p->isExpired('12-25', $jan, 30), 'Dec 25 is 16 days before Jan 10');
        $this->assertSame('2024-02-29', $p->resolveDate('02-29', $today)->format('Y-m-d'), 'leap day walks back');
    }

    public function test_parse_accepts_only_backup_artefacts()
    {
        $p = $this->paths();
        foreach ([
            'backups/11-19/newdrm-060000.sql.gz',
            'backups/11-19/newdrm-180000.sql.gz.sha256',
            'backups/11-19/newdrm-060000.sql.gz.part007',
            'backups/11-19/newdrm_files-060000.tar.gz.part000',
        ] as $ok) {
            $this->assertTrue($p->isArtefact($ok), $ok);
        }
        foreach ([
            'README.md', '.env', 'backups/11-19/.env',
            'backups/11-19/newdrm-070000.sql.gz',      // not a slot
            'backups/13-01/newdrm-060000.sql.gz',      // no month 13
            'backups/11-19/other_db-060000.sql.gz',
            'backups/11-19/newdrm-060000.tar.gz',      // db must be .sql.gz
            'backups/11-19/newdrm_files-060000.sql.gz',
            'backups/11-19/newdrm-060000.sql.gz.part000.sha256',
            'backups/11-19/newdrm-060000.sql.gz.age',
            'x/backups/11-19/newdrm-060000.sql.gz',
            'backups/11-19/sub/newdrm-060000.sql.gz',
        ] as $no) {
            $this->assertFalse($p->isArtefact($no), $no);
        }
    }

    public function test_group_collects_parts_and_checksum()
    {
        $g = $this->paths()->group([
            'README.md' => ['sha' => 'r', 'size' => 1],
            'backups/11-19/newdrm-060000.sql.gz.part001' => ['sha' => 'b', 'size' => 5],
            'backups/11-19/newdrm-060000.sql.gz.part000' => ['sha' => 'a', 'size' => 5],
            'backups/11-19/newdrm-060000.sql.gz.sha256' => ['sha' => 'c', 'size' => 1],
        ]);
        $this->assertSame(['backups/11-19/newdrm-060000.sql.gz'], array_keys($g));
        $this->assertSame([
            'backups/11-19/newdrm-060000.sql.gz.part000',
            'backups/11-19/newdrm-060000.sql.gz.part001',
            'backups/11-19/newdrm-060000.sql.gz.sha256',
        ], array_keys($g['backups/11-19/newdrm-060000.sql.gz']['members']));
    }
}
