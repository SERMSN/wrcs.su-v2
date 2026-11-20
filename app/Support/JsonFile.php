<?php

namespace App\Support;

use RuntimeException;

/**
 * Tiny, dependency-free JSON "database".
 *
 * Reads take a shared lock, updates take an exclusive lock and rewrite the
 * file in place, while full writes are atomic (temp file + rename).
 */
final class JsonFile
{
    /**
     * Decoded payloads, keyed by absolute path, cached for the current request.
     *
     * @var array<string, array<string, mixed>>
     */
    private static array $cache = [];

    /**
     * @return array<string, mixed>
     */
    public static function read(string $path, bool $fresh = false): array
    {
        if (! $fresh && array_key_exists($path, self::$cache)) {
            return self::$cache[$path];
        }

        if (! is_file($path)) {
            throw new RuntimeException("Data file not found: {$path}");
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException("Unable to open data file: {$path}");
        }

        try {
            flock($handle, LOCK_SH);
            $raw = stream_get_contents($handle);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }

        $decoded = json_decode($raw === false ? '' : $raw, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            throw new RuntimeException("Data file must contain a JSON object: {$path}");
        }

        return self::$cache[$path] = $decoded;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function write(string $path, array $data): void
    {
        $directory = dirname($path);
        self::ensureDirectory($directory);

        $json = self::encode($data);
        $temporary = tempnam($directory, '.json-');

        if ($temporary === false) {
            throw new RuntimeException("Unable to create a temp file in {$directory}");
        }

        try {
            if (file_put_contents($temporary, $json, LOCK_EX) === false) {
                throw new RuntimeException("Unable to write data file: {$path}");
            }

            @chmod($temporary, 0664);

            if (! rename($temporary, $path)) {
                throw new RuntimeException("Unable to replace data file: {$path}");
            }
        } finally {
            if (is_file($temporary)) {
                @unlink($temporary);
            }
        }

        self::$cache[$path] = $data;
    }

    /**
     * Atomically read-modify-write the file while holding an exclusive lock.
     *
     * @param  callable(array<string, mixed>): array<string, mixed>  $mutate
     * @return array<string, mixed>
     */
    public static function update(string $path, callable $mutate): array
    {
        $directory = dirname($path);
        self::ensureDirectory($directory);

        if (! is_file($path)) {
            self::write($path, []);
        }

        $handle = fopen($path, 'c+b');

        if ($handle === false) {
            throw new RuntimeException("Unable to open data file for update: {$path}");
        }

        try {
            if (! flock($handle, LOCK_EX)) {
                throw new RuntimeException("Unable to lock data file: {$path}");
            }

            $raw = stream_get_contents($handle);
            $decoded = $raw === false || trim($raw) === ''
                ? []
                : json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($decoded)) {
                throw new RuntimeException("Data file must contain a JSON object: {$path}");
            }

            $data = $mutate($decoded);

            if (! is_array($data)) {
                throw new RuntimeException('JSON mutator must return an array.');
            }

            $json = self::encode($data);

            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, $json);
            fflush($handle);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
            self::$cache[$path] = $data ?? [];
        }

        return self::$cache[$path];
    }

    public static function forget(?string $path = null): void
    {
        if ($path === null) {
            self::$cache = [];

            return;
        }

        unset(self::$cache[$path]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function encode(array $data): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ).PHP_EOL;
    }

    private static function ensureDirectory(string $directory): void
    {
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException("Unable to create data directory: {$directory}");
        }
    }
}
