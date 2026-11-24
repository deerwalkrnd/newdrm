<?php

namespace App\Services\Backup;

use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Support\Facades\Log;
use PDO;

/**
 * Dumps the MySQL/MariaDB database to a plain .sql file.
 *
 * method "auto" tries mysqldump (via proc_open, no shell) and falls back to
 * the pure-PHP dumper (ifsnop/mysqldump-php) if the binary is missing,
 * proc_open is disabled, or the dump fails or doesn't verify. That matters
 * here because nobody can install or check anything on the server.
 *
 * Both paths stream to disk. Both are verified: the "Dump completed" footer
 * is present and the number of routines and triggers in the file matches
 * information_schema (a user without the right grant gets a dump with no
 * routines and exit code 0).
 */
class DatabaseDumper
{
    /** @var string[] */
    public array $notes = [];

    public string $methodUsed = '';

    public function __construct(
        private array $conn,           // Laravel connection config
        private string $method,        // auto | mysqldump | php
        private string $binary,
        private string $tmpDir,
        // The app's own connection, used to count routines/triggers. A
        // read-only dump user without the routine grant can't see them in
        // information_schema either, so counting as that user proves nothing.
        private ?array $countConn = null,
    ) {
        if (($conn['driver'] ?? '') !== 'mysql') {
            throw new BackupException('only MySQL/MariaDB connections are supported, got ' . ($conn['driver'] ?? '?'));
        }
    }

    public static function fromConfig(): self
    {
        $c = config('dump-github');
        $conn = $appConn = config('database.connections.' . $c['database']['connection']);
        if (! is_array($conn)) {
            throw new BackupException('unknown database connection ' . $c['database']['connection']);
        }
        if (! empty($c['database']['username'])) {
            $conn['username'] = $c['database']['username'];
            $conn['password'] = (string) $c['database']['password'];
        }

        return new self($conn, $c['database']['method'], $c['database']['mysqldump_binary'], $c['tmp_dir'], $appConn);
    }

    public function dump(string $outFile): void
    {
        // Fail fast (and give a clear message) on bad credentials.
        $expected = $this->expectedCounts();

        $errors = [];
        if (in_array($this->method, ['auto', 'mysqldump'], true)) {
            $why = $this->mysqldumpUnavailable();
            if ($why === null) {
                try {
                    $this->runMysqldump($outFile);
                    $this->verify($outFile, $expected);
                    $this->methodUsed = 'mysqldump';

                    return;
                } catch (BackupException $e) {
                    $errors[] = 'mysqldump: ' . $e->getMessage();
                    @unlink($outFile);
                }
            } else {
                $errors[] = "mysqldump unavailable: $why";
            }
            if ($this->method === 'mysqldump') {
                throw new BackupException(implode('; ', $errors));
            }
            $this->notes[] = end($errors) . ' (falling back to the PHP dumper)';
            Log::warning('backup: ' . end($errors) . ', falling back to the PHP dumper');
        }

        try {
            $this->runPhpDumper($outFile);
            $this->verify($outFile, $expected);
            $this->methodUsed = 'php';
        } catch (\Throwable $e) {
            @unlink($outFile);
            $errors[] = 'php dumper: ' . $e->getMessage();
            throw new BackupException(implode('; ', $errors), 0, $e);
        }
    }

    /** @return array{routines: int, triggers: int} */
    public function expectedCounts(): array
    {
        try {
            $this->pdo()->query('SELECT 1'); // the dump user can log in
            $pdo = $this->countConn ? $this->pdo($this->countConn) : $this->pdo();
            $q = $pdo->prepare('SELECT
                (SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema = ?) AS r,
                (SELECT COUNT(*) FROM information_schema.triggers WHERE trigger_schema = ?) AS t');
            $q->execute([$this->conn['database'], $this->conn['database']]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new BackupException('cannot connect to the database: ' . $this->scrub($e->getMessage()), 0, $e);
        }

        return ['routines' => (int) $row['r'], 'triggers' => (int) $row['t']];
    }

    private function pdo(?array $conn = null): PDO
    {
        $conn ??= $this->conn;

        return new PDO($this->dsn(), $conn['username'], (string) ($conn['password'] ?? ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 20,
        ]);
    }

    private function dsn(): string
    {
        if (! empty($this->conn['unix_socket'])) {
            return 'mysql:unix_socket=' . $this->conn['unix_socket'] . ';dbname=' . $this->conn['database'] . ';charset=utf8mb4';
        }

        return 'mysql:host=' . $this->conn['host'] . ';port=' . ($this->conn['port'] ?? 3306) . ';dbname=' . $this->conn['database'] . ';charset=utf8mb4';
    }

    private function mysqldumpUnavailable(): ?string
    {
        if (! function_exists('proc_open')) {
            return 'proc_open is disabled';
        }
        [$code, $out] = $this->exec([$this->binary, '--version']);
        if ($code !== 0) {
            return "'{$this->binary} --version' failed (exit $code)";
        }
        // Keep the version line only (MariaDB also prints a deprecation notice).
        $lines = preg_grep('/(Ver|from) \d/', explode("\n", $out));
        $this->notes[] = trim((string) (end($lines) ?: $out));

        return null;
    }

    /** @return array{0: int, 1: string} exit code, stdout+stderr (small output only) */
    private function exec(array $cmd): array
    {
        $out = tempnam($this->tmpDir, 'out-');
        try {
            $p = @proc_open($cmd, [0 => ['file', $this->devNull(), 'r'], 1 => ['file', $out, 'w'], 2 => ['file', $out, 'a']], $pipes);
            if (! is_resource($p)) {
                return [127, ''];
            }

            return [proc_close($p), (string) file_get_contents($out, false, null, 0, 1 << 20)];
        } finally {
            @unlink($out);
        }
    }

    private function runMysqldump(string $outFile): void
    {
        $cnf = tempnam($this->tmpDir, 'my-');
        $err = tempnam($this->tmpDir, 'err-');
        try {
            chmod($cnf, 0600);
            $lines = ['[client]', 'user=' . $this->cnfValue($this->conn['username'])];
            $lines[] = 'password=' . $this->cnfValue((string) ($this->conn['password'] ?? ''));
            if (! empty($this->conn['unix_socket'])) {
                $lines[] = 'socket=' . $this->cnfValue($this->conn['unix_socket']);
            } else {
                $lines[] = 'host=' . $this->cnfValue($this->conn['host']);
                $lines[] = 'port=' . (int) ($this->conn['port'] ?? 3306);
            }
            // MariaDB 11 clients refuse a MySQL server's self-signed cert by
            // default; PDO (the app itself) doesn't verify it either. Only
            // MariaDB reads this group, so Oracle's mysqldump is unaffected.
            $lines[] = '[client-mariadb]';
            $lines[] = 'skip-ssl-verify-server-cert';
            file_put_contents($cnf, implode("\n", $lines) . "\n");

            $cmd = [
                $this->binary,
                '--defaults-extra-file=' . $cnf, // must be first
                '--single-transaction', '--quick', '--routines', '--triggers',
                '--no-tablespaces', '--hex-blob', '--default-character-set=utf8mb4',
            ];
            [, $help] = $this->exec([$this->binary, '--defaults-extra-file=' . $cnf, '--help']);
            if (str_contains($help, '--set-gtid-purged')) {
                $cmd[] = '--set-gtid-purged=OFF'; // MySQL only; MariaDB's has no such option
            }
            $cmd[] = '--result-file=' . $outFile;
            $cmd[] = $this->conn['database'];

            $p = proc_open($cmd, [0 => ['file', $this->devNull(), 'r'], 1 => ['file', $this->devNull(), 'w'], 2 => ['file', $err, 'w']], $pipes);
            if (! is_resource($p)) {
                throw new BackupException('cannot start mysqldump');
            }
            $code = proc_close($p);
            if ($code !== 0) {
                $msg = trim((string) file_get_contents($err, false, null, 0, 4000));
                throw new BackupException("exit $code: " . $this->scrub($msg));
            }
        } finally {
            @unlink($cnf);
            @unlink($err);
        }
    }

    private function runPhpDumper(string $outFile): void
    {
        if (! class_exists(Mysqldump::class)) {
            throw new BackupException('not installed (ifsnop/mysqldump-php is missing: the deploy did not run composer install)');
        }
        $dump = new Mysqldump($this->dsn(), $this->conn['username'], (string) ($this->conn['password'] ?? ''), [
            'single-transaction' => true,
            'lock-tables' => false,
            'routines' => true,
            'add-drop-table' => true,
            'add-drop-trigger' => true,
            'hex-blob' => true,
            'default-character-set' => Mysqldump::UTF8MB4,
            'skip-dump-date' => false,
            'skip-comments' => false,
        ], [PDO::ATTR_TIMEOUT => 20]);
        $dump->start($outFile);
    }

    /** @param array{routines: int, triggers: int} $expected */
    private function verify(string $file, array $expected): void
    {
        $fh = fopen($file, 'rb');
        if (! $fh) {
            throw new BackupException('dump file missing');
        }
        $routines = 0;
        $triggers = 0;
        $last = '';
        while (($line = fgets($fh)) !== false) {
            if (trim($line) !== '') {
                $last = $line;
            }
            if (! str_contains($line, 'CREATE')) {
                continue;
            }
            // Both dumpers write routines as
            //   CREATE DEFINER=`u`@`h` PROCEDURE `p`(...
            // and triggers as
            //   /*!50003 CREATE*/ /*!50017 DEFINER=`u`@`h`*/ /*!50003 TRIGGER `t` ...
            if (preg_match(self::createPattern('(?:PROCEDURE|FUNCTION)'), $line)) {
                $routines++;
            } elseif (preg_match(self::createPattern('TRIGGER'), $line)) {
                $triggers++;
            }
        }
        fclose($fh);

        if (! str_contains($last, 'Dump completed')) {
            throw new BackupException('dump is incomplete: last line is not "Dump completed"');
        }
        if ($routines !== $expected['routines']) {
            throw new BackupException("dump has $routines routines but the database has {$expected['routines']}"
                . ' (on MariaDB/MySQL 5.x the dump user needs SELECT ON mysql.proc; on MySQL 8 SHOW_ROUTINE)');
        }
        if ($triggers !== $expected['triggers']) {
            throw new BackupException("dump has $triggers triggers but the database has {$expected['triggers']} (the dump user needs TRIGGER)");
        }
    }

    public static function createPattern(string $kind): string
    {
        $c = '(?:\/\*!\d+\s+)?';           // optional version comment opener
        $e = '(?:\s*\*\/)?';               // optional closer
        $ident = '(?:`(?:[^`]|``)*`|[^\s@`*]+)';
        $definer = "(?:{$c}DEFINER\s*=\s*{$ident}(?:@{$ident})?{$e}\s+)?";

        return "/^{$c}CREATE{$e}\s+{$definer}{$c}{$kind}\s+`/i";
    }

    private function cnfValue(string $v): string
    {
        if (preg_match('/[\r\n]/', $v)) {
            throw new BackupException('database credentials contain a newline');
        }

        return '"' . addcslashes($v, "\\\"") . '"';
    }

    private function scrub(string $msg): string
    {
        foreach ([$this->conn, $this->countConn ?? []] as $c) {
            $pw = (string) ($c['password'] ?? '');
            if ($pw !== '') {
                $msg = str_replace($pw, '***', $msg);
            }
        }

        return $msg;
    }

    private function devNull(): string
    {
        return DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null';
    }
}
