<?php

namespace App\Services\Backup;

/**
 * File-to-file helpers. Everything works in 1 MiB chunks so memory use does
 * not depend on the size of the dump.
 */
class FileTools
{
    public const CHUNK = 1048576;

    public static function gzip(string $src, string $dst, int $level = 6): void
    {
        $in = fopen($src, 'rb');
        $out = gzopen($dst, 'wb' . $level);
        if (! $in || ! $out) {
            throw new BackupException("cannot gzip $src");
        }
        while (! feof($in)) {
            $chunk = fread($in, self::CHUNK);
            if ($chunk === false) {
                throw new BackupException("read error on $src");
            }
            if ($chunk !== '' && gzwrite($out, $chunk) !== strlen($chunk)) {
                throw new BackupException("write error on $dst (disk full?)");
            }
        }
        fclose($in);
        if (! gzclose($out)) {
            throw new BackupException("cannot finish $dst");
        }
    }

    /**
     * The equivalent of `gzip -t`: inflate the whole file, checking the CRC
     * and length trailer. Returns the last $tailBytes of the uncompressed data.
     */
    public static function testGzip(string $file, int $tailBytes = 4096): string
    {
        $in = fopen($file, 'rb');
        if (! $in) {
            throw new BackupException("cannot open $file");
        }
        $ctx = inflate_init(ZLIB_ENCODING_GZIP);
        $tail = '';
        $ended = false;
        while (! feof($in)) {
            $chunk = fread($in, self::CHUNK);
            if ($chunk === false || $chunk === '') {
                continue;
            }
            if ($ended) {
                throw new BackupException("$file has trailing data after the gzip stream");
            }
            $plain = @inflate_add($ctx, $chunk, ZLIB_SYNC_FLUSH);
            if ($plain === false) {
                throw new BackupException("$file is not valid gzip (corrupt data or bad CRC)");
            }
            $tail = substr($tail . $plain, -$tailBytes);
            $ended = inflate_get_status($ctx) === ZLIB_STREAM_END;
        }
        fclose($in);
        if (! $ended) {
            throw new BackupException("$file is truncated (gzip stream did not end)");
        }

        return $tail;
    }

    public static function sha256(string $file): string
    {
        $h = hash_file('sha256', $file);
        if ($h === false) {
            throw new BackupException("cannot hash $file");
        }

        return $h;
    }

    /**
     * Split $file into $file.part000, .part001, ... when it is larger than
     * $bytes. Returns the files to upload, in order.
     *
     * @return string[]
     */
    public static function split(string $file, int $bytes): array
    {
        $size = filesize($file);
        if ($size <= $bytes) {
            return [$file];
        }
        $in = fopen($file, 'rb');
        $parts = [];
        for ($i = 0; ! feof($in); $i++) {
            $name = sprintf('%s.part%03d', $file, $i);
            $out = fopen($name, 'wb');
            $left = $bytes;
            while ($left > 0 && ! feof($in)) {
                $chunk = fread($in, min(self::CHUNK, $left));
                if ($chunk === false) {
                    throw new BackupException("read error on $file");
                }
                if (fwrite($out, $chunk) !== strlen($chunk)) {
                    throw new BackupException("write error on $name (disk full?)");
                }
                $left -= strlen($chunk);
            }
            fclose($out);
            if (filesize($name) === 0) {
                unlink($name);
                break;
            }
            $parts[] = $name;
        }
        fclose($in);
        unlink($file);

        return $parts;
    }

    public static function startsWith(string $file, string $magic): bool
    {
        $fh = @fopen($file, 'rb');
        if (! $fh) {
            return false;
        }
        $head = fread($fh, strlen($magic));
        fclose($fh);

        return $head === $magic;
    }

    public static function removeTree(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            $f->isDir() && ! $f->isLink() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
        }
        @rmdir($dir);
    }
}
