<?php

declare(strict_types=1);

use KevinPijning\Prompt\Promptfoo\CacheMerger;

function recursiveCleanup(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }

    $items = glob($dir . '/{,.}[!.,!..]*', GLOB_BRACE);
    if ($items === false) {
        $items = [];
    }

    foreach ($items as $item) {
        if (is_dir($item)) {
            recursiveCleanup($item);
        } else {
            @unlink($item);
        }
    }

    @rmdir($dir);
}

beforeEach(function () {
    $tempDir = sys_get_temp_dir();
    
    $cacheTestDirs = glob($tempDir . '/promptfoo_cache_test_*');
    if ($cacheTestDirs !== false) {
        foreach ($cacheTestDirs as $dir) {
            recursiveCleanup($dir);
        }
    }

    $homeDirs = glob($tempDir . '/promptfoo_home_*');
    if ($homeDirs !== false) {
        foreach ($homeDirs as $dir) {
            recursiveCleanup($dir);
        }
    }
    
    $parallelDirs = glob($tempDir . '/promptfoo_parallel_cache_*');
    if ($parallelDirs !== false) {
        foreach ($parallelDirs as $dir) {
            recursiveCleanup($dir);
        }
    }
});

afterEach(function () {
    unset($_ENV['HOME'], $_ENV['USERPROFILE']);
});

test('does nothing when no parallel cache directories exist', function () {
    $_ENV['HOME'] = sys_get_temp_dir() . '/promptfoo_home_' . uniqid();
    mkdir($_ENV['HOME'] . '/.promptfoo/cache', 0755, true);

    CacheMerger::mergeParallelCaches();

    expect(file_exists($_ENV['HOME'] . '/.promptfoo/cache/cache.json'))->toBeFalse();
    
    cleanupDirectory($_ENV['HOME']);
});

test('does nothing when home directory is empty', function () {
    unset($_ENV['HOME'], $_ENV['USERPROFILE']);
    putenv('HOME');
    putenv('USERPROFILE');

    $cacheDir = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_no_home';
    createParallelCacheDir($cacheDir, ['key' => ['value' => 'test']]);

    CacheMerger::mergeParallelCaches();

    expect(is_dir($cacheDir))->toBeTrue();
    
    cleanupDirectory($cacheDir);
});

test('merges single parallel cache directory', function () {
    $_ENV['HOME'] = sys_get_temp_dir() . '/promptfoo_home_' . uniqid();
    mkdir($_ENV['HOME'] . '/.promptfoo/cache', 0755, true);
    
    $cacheDir = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_single';
    
    createParallelCacheDir($cacheDir, [
        'key-1' => ['expire' => 111, 'value' => 'value-1'],
    ]);

    CacheMerger::mergeParallelCaches();

    $mainCache = loadMainCache($_ENV['HOME']);
    expect($mainCache['entries'])->toHaveKey('key-1');
    expect($mainCache['entries']['key-1']['value'])->toBe('value-1');
    
    cleanupDirectory($cacheDir);
    cleanupDirectory($_ENV['HOME']);
});

test('merges multiple parallel cache directories', function () {
    $_ENV['HOME'] = sys_get_temp_dir() . '/promptfoo_home_' . uniqid();
    mkdir($_ENV['HOME'] . '/.promptfoo/cache', 0755, true);

    $cacheDir1 = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_merge_123';
    $cacheDir2 = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_merge_456';
    
    createParallelCacheDir($cacheDir1, [
        'key-1' => ['expire' => 111, 'value' => 'value-1'],
    ]);
    createParallelCacheDir($cacheDir2, [
        'key-2' => ['expire' => 222, 'value' => 'value-2'],
    ]);
    createMainCache($_ENV['HOME'], [
        'existing-key' => ['expire' => 999, 'value' => 'existing-value'],
    ]);

    CacheMerger::mergeParallelCaches();

    $mainCache = loadMainCache($_ENV['HOME']);
    
    expect($mainCache['entries'])->toHaveKey('existing-key')
        ->toHaveKey('key-1')
        ->toHaveKey('key-2');
    
    expect($mainCache['entries']['existing-key']['value'])->toBe('existing-value');
    expect($mainCache['entries']['key-1']['value'])->toBe('value-1');
    expect($mainCache['entries']['key-2']['value'])->toBe('value-2');
    
    cleanupDirectory($cacheDir1);
    cleanupDirectory($cacheDir2);
    cleanupDirectory($_ENV['HOME']);
});

test('handles duplicate keys by keeping first occurrence', function () {
    $_ENV['HOME'] = sys_get_temp_dir() . '/promptfoo_home_' . uniqid();
    mkdir($_ENV['HOME'] . '/.promptfoo/cache', 0755, true);

    $cacheDir1 = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_dup_123';
    $cacheDir2 = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_dup_456';

    createParallelCacheDir($cacheDir1, [
        'duplicate-key' => ['expire' => 111, 'value' => 'first-value'],
    ]);
    createParallelCacheDir($cacheDir2, [
        'duplicate-key' => ['expire' => 222, 'value' => 'second-value'],
    ]);

    CacheMerger::mergeParallelCaches();

    $mainCache = loadMainCache($_ENV['HOME']);
    
    expect($mainCache['entries'])->toHaveKey('duplicate-key');
    expect($mainCache['entries']['duplicate-key']['value'])->toBe('first-value');
    
    cleanupDirectory($cacheDir1);
    cleanupDirectory($cacheDir2);
    cleanupDirectory($_ENV['HOME']);
});

test('creates main cache if not exists', function () {
    $_ENV['HOME'] = sys_get_temp_dir() . '/promptfoo_home_' . uniqid();
    mkdir($_ENV['HOME'] . '/.promptfoo/cache', 0755, true);

    $cacheDir = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_new_123';

    createParallelCacheDir($cacheDir, [
        'new-key' => ['expire' => 123, 'value' => 'new-value'],
    ]);

    CacheMerger::mergeParallelCaches();

    $mainCache = loadMainCache($_ENV['HOME']);
    
    expect($mainCache['entries'])->toHaveKey('new-key');
    expect($mainCache['entries']['new-key']['value'])->toBe('new-value');
    
    cleanupDirectory($cacheDir);
    cleanupDirectory($_ENV['HOME']);
});

test('handles corrupted cache files gracefully', function () {
    $_ENV['HOME'] = sys_get_temp_dir() . '/promptfoo_home_' . uniqid();
    mkdir($_ENV['HOME'] . '/.promptfoo/cache', 0755, true);

    $cacheDir1 = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_corrupt_123';
    createParallelCacheDir($cacheDir1, ['valid-key' => ['expire' => 111, 'value' => 'valid-value']]);
    
    $cacheDir2 = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_corrupt_456';
    mkdir($cacheDir2);
    file_put_contents($cacheDir2 . '/cache.json', '{ invalid json');

    CacheMerger::mergeParallelCaches();

    $mainCache = loadMainCache($_ENV['HOME']);
    
    expect($mainCache['entries'])->toHaveKey('valid-key');
    expect($mainCache['entries']['valid-key']['value'])->toBe('valid-value');
    
    expect(is_dir($cacheDir1))->toBeFalse();
    expect(is_dir($cacheDir2))->toBeFalse();
    
    cleanupDirectory($cacheDir1);
    cleanupDirectory($cacheDir2);
    cleanupDirectory($_ENV['HOME']);
});

test('handles empty parallel cache', function () {
    $_ENV['HOME'] = sys_get_temp_dir() . '/promptfoo_home_' . uniqid();
    mkdir($_ENV['HOME'] . '/.promptfoo/cache', 0755, true);

    $cacheDir = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_empty';

    createParallelCacheDir($cacheDir, []);

    CacheMerger::mergeParallelCaches();

    $mainCache = loadMainCache($_ENV['HOME']);
    expect($mainCache['entries'])->toBeEmpty();
    
    cleanupDirectory($cacheDir);
    cleanupDirectory($_ENV['HOME']);
});

test('cleans up parallel cache directories after merge', function () {
    $_ENV['HOME'] = sys_get_temp_dir() . '/promptfoo_home_' . uniqid();
    mkdir($_ENV['HOME'] . '/.promptfoo/cache', 0755, true);

    $cacheDir = sys_get_temp_dir() . '/promptfoo_parallel_cache_test_cleanup';
    createParallelCacheDir($cacheDir, ['key' => ['value' => 'test']]);

    expect(is_dir($cacheDir))->toBeTrue();

    CacheMerger::mergeParallelCaches();

    expect(is_dir($cacheDir))->toBeFalse();
    
    cleanupDirectory($_ENV['HOME']);
});

function createParallelCacheDir(string $dir, array $entries = []): void
{
    mkdir($dir, 0755, true);
    
    $cacheData = ['cache' => []];
    foreach ($entries as $key => $entry) {
        $cacheData['cache'][] = [$key, $entry];
    }

    file_put_contents($dir . '/cache.json', json_encode($cacheData, JSON_PRETTY_PRINT));
}

function createMainCache(string $homeDir, array $entries = []): void
{
    $cachePath = $homeDir . '/.promptfoo/cache/cache.json';
    
    $cacheData = ['cache' => []];
    foreach ($entries as $key => $entry) {
        $cacheData['cache'][] = [$key, $entry];
    }

    file_put_contents($cachePath, json_encode($cacheData, JSON_PRETTY_PRINT));
}

function loadMainCache(string $homeDir): array
{
    $cachePath = $homeDir . '/.promptfoo/cache/cache.json';
    
    if (! file_exists($cachePath)) {
        return ['entries' => []];
    }

    $data = json_decode(file_get_contents($cachePath), true);
    
    if (! is_array($data) || ! isset($data['cache'])) {
        return ['entries' => []];
    }

    $entries = [];
    foreach ($data['cache'] as $item) {
        if (is_array($item) && count($item) >= 2) {
            $key = $item[0];
            $entry = $item[1];
            
            if (is_string($key) && is_array($entry)) {
                $entries[$key] = $entry;
            }
        }
    }

    return ['entries' => $entries];
}

function cleanupDirectory(string $dir): void
{
    recursiveCleanup($dir);
}
