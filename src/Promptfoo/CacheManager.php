<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;

final class CacheManager
{
    private static ?CachePath $cachePath = null;

    public static function setCachePath(?CachePath $cachePath): void
    {
        self::$cachePath = $cachePath;
    }

    public static function reset(): void
    {
        self::$cachePath = null;
    }

    public static function initializeParallelCache(): ?string
    {
        $cachePath = self::getCachePath();
        $pid = self::getPid();

        if (! $cachePath->isValid() || $pid === null) {
            return null;
        }

        $parallelCacheDir = $cachePath->parallelCacheDir($pid);
        $parallelCacheFile = $cachePath->parallelCache($pid);

        if (file_exists($parallelCacheFile)) {
            return $parallelCacheDir;
        }

        $mainCachePath = $cachePath->mainCache();

        if (! file_exists($mainCachePath)) {
            return $parallelCacheDir;
        }

        if (! is_dir($parallelCacheDir)) {
            mkdir($parallelCacheDir, 0755, true);
        }

        copy($mainCachePath, $parallelCacheFile);

        return $parallelCacheDir;
    }

    public static function mergeParallelCaches(): void
    {
        $cachePath = self::getCachePath();

        if (! $cachePath->isValid()) {
            return;
        }

        $parallelDir = $cachePath->parallelDir();

        if (! is_dir($parallelDir)) {
            return;
        }

        $workerDirs = self::listWorkerDirectories($parallelDir);

        if ($workerDirs === []) {
            return;
        }

        $mergedEntries = self::loadMainCache($cachePath);

        foreach ($workerDirs as $workerDir) {
            $cacheFile = $workerDir.'/cache.json';

            if (! file_exists($cacheFile)) {
                continue;
            }

            try {
                $parallelEntries = self::parseCacheFile($cacheFile);
                $mergedEntries = $parallelEntries + $mergedEntries;
            } catch (Throwable $e) {
                error_log(sprintf(
                    'Failed to merge cache from %s: %s',
                    $workerDir,
                    $e->getMessage()
                ));
            }
        }

        self::saveMainCache($cachePath, $mergedEntries);
        self::deleteDirectory($parallelDir);
    }

    private static function getCachePath(): CachePath
    {
        return self::$cachePath ??= CachePath::fromEnvironment();
    }

    private static function getPid(): ?int
    {
        $pid = getmypid();

        return $pid === false ? null : $pid;
    }

    /**
     * @return list<string>
     */
    private static function listWorkerDirectories(string $parallelDir): array
    {
        $entries = scandir($parallelDir);

        if ($entries === false) {
            return [];
        }

        $dirs = [];
        foreach ($entries as $entry) {
            if ($entry === '.') {
                continue;
            }
            if ($entry === '..') {
                continue;
            }
            $fullPath = $parallelDir.'/'.$entry;
            if (is_dir($fullPath)) {
                $dirs[] = $fullPath;
            }
        }

        return $dirs;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function loadMainCache(CachePath $cachePath): array
    {
        $mainCachePath = $cachePath->mainCache();

        if (! file_exists($mainCachePath)) {
            return [];
        }

        return self::parseCacheFile($mainCachePath);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function parseCacheFile(string $filePath): array
    {
        $json = file_get_contents($filePath);
        if ($json === false) {
            return [];
        }

        $data = json_decode($json, true);

        if (! is_array($data) || ! isset($data['cache']) || ! is_array($data['cache'])) {
            return [];
        }

        // Keyv format: {"cache": [["key", {value}], ["key2", {value2}]]}
        /** @var array<string, array<string, mixed>> */
        return array_column($data['cache'], 1, 0);
    }

    /**
     * @param  array<string, array<string, mixed>>  $entries
     */
    private static function saveMainCache(CachePath $cachePath, array $entries): void
    {
        $mainCachePath = $cachePath->mainCache();
        $cacheDir = dirname($mainCachePath);

        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Convert to keyv format: [["key", {value}], ...]
        $cacheData = ['cache' => array_map(
            static fn (string $key, array $value): array => [$key, $value],
            array_keys($entries),
            array_values($entries)
        )];

        file_put_contents($mainCachePath, json_encode($cacheData, JSON_PRETTY_PRINT));
    }

    private static function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @rmdir($file->getPathname());
            } else {
                @unlink($file->getPathname());
            }
        }

        @rmdir($dir);
    }
}
