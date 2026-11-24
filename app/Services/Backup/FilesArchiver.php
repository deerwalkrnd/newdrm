<?php

namespace App\Services\Backup;

/**
 * Writes a .tar.gz of the uploads folder in pure PHP (no tar binary),
 * streaming each file in 1 MiB chunks. ustar format; names that don't fit
 * ustar get a GNU long-name record, which GNU tar and bsdtar both read.
 * Symlinks are skipped.
 */
class FilesArchiver
{
    public int $fileCount = 0;

    /**
     * @param  string[]  $exclude  paths relative to $root
     */
    public function archive(string $root, array $exclude, string $outFile): void
    {
        $root = rtrim(str_replace('\\', '/', realpath($root) ?: $root), '/');
        if (! is_dir($root)) {
            throw new BackupException("uploads folder $root does not exist");
        }
        $gz = gzopen($outFile, 'wb6');
        if (! $gz) {
            throw new BackupException("cannot write $outFile");
        }
        $exclude = array_map(fn ($e) => trim(str_replace('\\', '/', $e), '/'), $exclude);

        $it = new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
                function (\SplFileInfo $f) use ($root, $exclude) {
                    if ($f->isLink()) {
                        return false;
                    }
                    $rel = ltrim(substr(str_replace('\\', '/', $f->getPathname()), strlen($root)), '/');
                    foreach ($exclude as $e) {
                        if ($rel === $e || str_starts_with($rel, $e . '/')) {
                            return false;
                        }
                    }

                    return true;
                }
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $entries = [];
        foreach ($it as $f) {
            $entries[] = $f;
        }
        // Stable order makes archives comparable between runs.
        usort($entries, fn ($a, $b) => strcmp($a->getPathname(), $b->getPathname()));

        foreach ($entries as $f) {
            $rel = ltrim(substr(str_replace('\\', '/', $f->getPathname()), strlen($root)), '/');
            if ($f->isDir()) {
                $this->writeHeader($gz, $rel . '/', 0, '5', $f->getMTime(), 0755);
            } elseif ($f->isFile()) {
                $size = $f->getSize();
                $this->writeHeader($gz, $rel, $size, '0', $f->getMTime(), 0644);
                $in = fopen($f->getPathname(), 'rb');
                if (! $in) {
                    throw new BackupException("cannot read upload $rel");
                }
                $written = 0;
                while (! feof($in)) {
                    $chunk = fread($in, FileTools::CHUNK);
                    if ($chunk === false) {
                        throw new BackupException("read error on upload $rel");
                    }
                    // A file that grows mid-archive must not overrun its header.
                    $chunk = substr($chunk, 0, $size - $written);
                    if ($chunk === '') {
                        break;
                    }
                    $this->write($gz, $chunk);
                    $written += strlen($chunk);
                }
                fclose($in);
                if ($written < $size) { // file shrank: pad with zeros
                    $this->write($gz, str_repeat("\0", $size - $written));
                }
                $pad = (512 - $size % 512) % 512;
                if ($pad) {
                    $this->write($gz, str_repeat("\0", $pad));
                }
                $this->fileCount++;
            }
        }
        $this->write($gz, str_repeat("\0", 1024)); // end-of-archive
        if (! gzclose($gz)) {
            throw new BackupException("cannot finish $outFile");
        }
    }

    private function writeHeader($gz, string $name, int $size, string $type, int $mtime, int $mode): void
    {
        [$prefix, $short] = $this->splitName($name);
        if ($short === null) {
            // GNU long name: a "././@LongLink" record holding the full name.
            $long = $name . "\0";
            $this->write($gz, $this->header('././@LongLink', '', strlen($long), 'L', 0, 0644));
            $this->write($gz, str_pad($long, (int) ceil(strlen($long) / 512) * 512, "\0"));
            $prefix = '';
            $short = substr($name, 0, 100);
        }
        $this->write($gz, $this->header($short, $prefix, $size, $type, $mtime, $mode));
    }

    /** @return array{0: string, 1: ?string} [prefix, name] or [.., null] if it can't fit ustar */
    private function splitName(string $name): array
    {
        if (strlen($name) <= 100) {
            return ['', $name];
        }
        $trimmed = rtrim($name, '/');
        $pos = strrpos(substr($trimmed, 0, 156), '/');
        while ($pos !== false) {
            $prefix = substr($name, 0, $pos);
            $rest = substr($name, $pos + 1);
            if (strlen($prefix) <= 155 && strlen($rest) <= 100 && $rest !== '') {
                return [$prefix, $rest];
            }
            $pos = $pos > 0 ? strrpos(substr($trimmed, 0, $pos), '/') : false;
        }

        return ['', null];
    }

    private function header(string $name, string $prefix, int $size, string $type, int $mtime, int $mode): string
    {
        $h = str_pad($name, 100, "\0")
            . sprintf('%07o', $mode) . "\0"
            . sprintf('%07o', 0) . "\0"
            . sprintf('%07o', 0) . "\0"
            . sprintf('%011o', $size) . "\0"
            . sprintf('%011o', max(0, $mtime)) . "\0"
            . '        '               // checksum placeholder
            . $type
            . str_repeat("\0", 100)    // linkname
            . "ustar\0" . '00'
            . str_pad('root', 32, "\0")
            . str_pad('root', 32, "\0")
            . str_repeat("\0", 16)     // devmajor, devminor
            . str_pad($prefix, 155, "\0")
            . str_repeat("\0", 12);
        $sum = 0;
        for ($i = 0; $i < 512; $i++) {
            $sum += ord($h[$i]);
        }

        return substr_replace($h, sprintf('%06o', $sum) . "\0 ", 148, 8);
    }

    private function write($gz, string $data): void
    {
        if (gzwrite($gz, $data) !== strlen($data)) {
            throw new BackupException('write error on files archive (disk full?)');
        }
    }
}
