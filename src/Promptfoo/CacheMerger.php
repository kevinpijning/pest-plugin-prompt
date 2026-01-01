<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use Throwable;

final class CacheMerger
{
    private const DEFAULT_CACHE_PATH = '%s/.promptfoo/cache/cache.json';

    public static function mergeParallelCaches(): void
    {
        $homeDir = self::getHomeDirectory();
        if ($homeDir === '') {
            return;
        }

        $tempDir = sys_get_temp_dir();
        $parallelPattern = $tempDir.'/promptfoo_parallel_cache_*';
        $parallelDirs = glob($parallelPattern);

        if ($parallelDirs === [] || $parallelDirs === false) {
            return;
        }

        $mainCache = self::loadMainCache();
        /** @var array<string, array<string, mixed>> $mergedEntries */
        $mergedEntries = $mainCache['entries'];

        foreach ($parallelDirs as $parallelDir) {
            $cacheFile = $parallelDir.'/cache.json';

            if (! file_exists($cacheFile)) {
                continue;
            }

            try {
                $parallelCache = self::parseCacheFile($cacheFile);
                /** @var array<string, array<string, mixed>> $parallelEntries */
                $parallelEntries = $parallelCache['entries'];
                $mergedEntries = self::mergeEntries($mergedEntries, $parallelEntries);
            } catch (Throwable $e) {
                error_log(sprintf(
                    'Failed to merge cache from %s: %s',
                    $parallelDir,
                    $e->getMessage()
                ));

                continue;
            }

            self::cleanupDirectory($parallelDir);
        }

        self::saveMainCache($mergedEntries);
    }

    private static function getHomeDirectory(): string
    {
        return $_ENV['HOME'] ?? getenv('HOME') ?: $_ENV['USERPROFILE'] ?? getenv('USERPROFILE') ?: '';
    }

    /**
     * Load the main cache file
     *
     * @return array{entries: array<string, array<string, mixed>>}
     */
    private static function loadMainCache(): array
    {
        $cachePath = sprintf(self::DEFAULT_CACHE_PATH, self::getHomeDirectory());

        if (! file_exists($cachePath)) {
            return ['entries' => []];
        }

        return self::parseCacheFile($cachePath);
    }

    /**
     * Parse a cache file into its entries array
     *
     * @return array{entries: array<string, array<string, mixed>>}
     */
    private static function parseCacheFile(string $filePath): array
    {
        $json = file_get_contents($filePath);
        if ($json === false) {
            return ['entries' => []];
        }

        $data = json_decode($json, true);

        if (! is_array($data) || ! isset($data['cache'])) {
            return ['entries' => []];
        }

        // Keyv cache format: {"cache": [["keyv:...", {"expire": timestamp, "value": "..."}]]}
        /** @var array<string, array<string, mixed>> $entries */
        $entries = [];
        foreach ($data['cache'] as $item) {
            if (is_array($item) && count($item) === 2) {
                $key = $item[0];
                $value = $item[1];

                // Remove 'keyv:' prefix if present
                if (str_starts_with((string) $key, 'keyv:')) {
                    $key = substr((string) $key, 5);
                }

                if (is_string($key) && is_array($value)) {
                    $entries[$key] = $value;
                }
            }
        }

        return ['entries' => $entries];
    }

    /**
     * Merge entries from parallel cache into main cache
     *
     * @param  array<string, array<string, mixed>>  $mainEntries  The main cache entries
     * @param  array<string, array<string, mixed>>  $parallelEntries  The parallel cache entries
     * @return array<string, array<string, mixed>> Merged entries
     */
    private static function mergeEntries(array $mainEntries, array $parallelEntries): array
    {
        foreach ($parallelEntries as $key => $entry) {
            if (! isset($mainEntries[$key])) {
                $mainEntries[$key] = $entry;
            }
        }

        return $mainEntries;
    }

    /**
     * Save the merged entries to the main cache file
     *
     * @param  array<string, array<string, mixed>>  $entries  The merged entries to save
     */
    private static function saveMainCache(array $entries): void
    {
        $cachePath = sprintf(self::DEFAULT_CACHE_PATH, self::getHomeDirectory());
        $cacheDir = dirname($cachePath);

        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cacheData = ['cache' => []];
        foreach ($entries as $key => $entry) {
            $cacheData['cache'][] = [$key, $entry];
        }

        file_put_contents($cachePath, json_encode($cacheData, JSON_PRETTY_PRINT));
    }

    /**
     * Clean up a directory and its contents
     */
    private static function cleanupDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = glob($dir.'/*');
        if ($files === false) {
            $files = [];
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        rmdir($dir);
    }
}
