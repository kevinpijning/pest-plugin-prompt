<?php

declare(strict_types=1);

use KevinPijning\Prompt\Promptfoo\CacheManager;
use KevinPijning\Prompt\Promptfoo\CachePath;

function createTestCachePath(): CachePath
{
    $homeDir = sys_get_temp_dir().'/promptfoo_home_'.uniqid();
    mkdir($homeDir.'/.promptfoo/cache', 0755, true);

    return new CachePath($homeDir);
}

function createParallelCache(CachePath $cachePath, int $pid, array $entries): void
{
    $dir = $cachePath->parallelCacheDir($pid);
    mkdir($dir, 0755, true);

    $cacheData = ['cache' => []];
    foreach ($entries as $key => $entry) {
        $cacheData['cache'][] = [$key, $entry];
    }

    file_put_contents($cachePath->parallelCache($pid), json_encode($cacheData, JSON_PRETTY_PRINT));
}

function createMainCacheFile(CachePath $cachePath, array $entries): void
{
    $cacheData = ['cache' => []];
    foreach ($entries as $key => $entry) {
        $cacheData['cache'][] = [$key, $entry];
    }

    file_put_contents($cachePath->mainCache(), json_encode($cacheData, JSON_PRETTY_PRINT));
}

function loadMainCacheFile(CachePath $cachePath): array
{
    $path = $cachePath->mainCache();

    if (! file_exists($path)) {
        return ['entries' => []];
    }

    $data = json_decode(file_get_contents($path), true);

    if (! is_array($data) || ! isset($data['cache'])) {
        return ['entries' => []];
    }

    return ['entries' => array_column($data['cache'], 1, 0)];
}

function deleteTestDirectory(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }

    $items = glob($dir.'/{,.}[!.,!..]*', GLOB_BRACE);
    if ($items === false) {
        $items = [];
    }

    foreach ($items as $item) {
        if (is_dir($item)) {
            deleteTestDirectory($item);
        } else {
            @unlink($item);
        }
    }

    @rmdir($dir);
}

beforeEach(function () {
    CacheManager::reset();
});

afterEach(function () {
    CacheManager::reset();
});

test('mergeParallelCaches does nothing when no parallel cache directories exist', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    CacheManager::mergeParallelCaches();

    expect(file_exists($cachePath->mainCache()))->toBeFalse();

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('mergeParallelCaches does nothing when cache path is invalid', function () {
    CacheManager::setCachePath(new CachePath(''));

    CacheManager::mergeParallelCaches();

    expect(true)->toBeTrue();
});

test('mergeParallelCaches merges single parallel cache directory', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createParallelCache($cachePath, 12345, [
        'keyv:fetch:v2:key-1' => ['expire' => 111, 'value' => 'value-1'],
    ]);

    CacheManager::mergeParallelCaches();

    $mainCache = loadMainCacheFile($cachePath);
    expect($mainCache['entries'])->toHaveKey('keyv:fetch:v2:key-1');
    expect($mainCache['entries']['keyv:fetch:v2:key-1']['value'])->toBe('value-1');

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('mergeParallelCaches merges multiple parallel cache directories', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createParallelCache($cachePath, 123, [
        'keyv:fetch:v2:key-1' => ['expire' => 111, 'value' => 'value-1'],
    ]);
    createParallelCache($cachePath, 456, [
        'keyv:fetch:v2:key-2' => ['expire' => 222, 'value' => 'value-2'],
    ]);
    createMainCacheFile($cachePath, [
        'keyv:fetch:v2:existing-key' => ['expire' => 999, 'value' => 'existing-value'],
    ]);

    CacheManager::mergeParallelCaches();

    $mainCache = loadMainCacheFile($cachePath);

    expect($mainCache['entries'])->toHaveKey('keyv:fetch:v2:existing-key')
        ->toHaveKey('keyv:fetch:v2:key-1')
        ->toHaveKey('keyv:fetch:v2:key-2');

    expect($mainCache['entries']['keyv:fetch:v2:existing-key']['value'])->toBe('existing-value');
    expect($mainCache['entries']['keyv:fetch:v2:key-1']['value'])->toBe('value-1');
    expect($mainCache['entries']['keyv:fetch:v2:key-2']['value'])->toBe('value-2');

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('mergeParallelCaches parallel entries take precedence over existing entries', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createMainCacheFile($cachePath, [
        'keyv:fetch:v2:duplicate-key' => ['expire' => 111, 'value' => 'old-value'],
    ]);
    createParallelCache($cachePath, 123, [
        'keyv:fetch:v2:duplicate-key' => ['expire' => 222, 'value' => 'new-value'],
    ]);

    CacheManager::mergeParallelCaches();

    $mainCache = loadMainCacheFile($cachePath);

    expect($mainCache['entries'])->toHaveKey('keyv:fetch:v2:duplicate-key');
    expect($mainCache['entries']['keyv:fetch:v2:duplicate-key']['value'])->toBe('new-value');

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('mergeParallelCaches creates main cache if not exists', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createParallelCache($cachePath, 123, [
        'keyv:fetch:v2:new-key' => ['expire' => 123, 'value' => 'new-value'],
    ]);

    CacheManager::mergeParallelCaches();

    $mainCache = loadMainCacheFile($cachePath);

    expect($mainCache['entries'])->toHaveKey('keyv:fetch:v2:new-key');
    expect($mainCache['entries']['keyv:fetch:v2:new-key']['value'])->toBe('new-value');

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('mergeParallelCaches handles corrupted cache files gracefully', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createParallelCache($cachePath, 123, [
        'keyv:fetch:v2:valid-key' => ['expire' => 111, 'value' => 'valid-value'],
    ]);

    $corruptDir = $cachePath->parallelCacheDir(456);
    mkdir($corruptDir, 0755, true);
    file_put_contents($cachePath->parallelCache(456), '{ invalid json');

    CacheManager::mergeParallelCaches();

    $mainCache = loadMainCacheFile($cachePath);

    expect($mainCache['entries'])->toHaveKey('keyv:fetch:v2:valid-key');
    expect($mainCache['entries']['keyv:fetch:v2:valid-key']['value'])->toBe('valid-value');

    expect(is_dir($cachePath->parallelDir()))->toBeFalse();

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('mergeParallelCaches handles empty parallel cache', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createParallelCache($cachePath, 123, []);

    CacheManager::mergeParallelCaches();

    $mainCache = loadMainCacheFile($cachePath);
    expect($mainCache['entries'])->toBeEmpty();

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('mergeParallelCaches cleans up entire parallel directory after merge', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createParallelCache($cachePath, 123, ['keyv:fetch:v2:key' => ['value' => 'test']]);
    createParallelCache($cachePath, 456, ['keyv:fetch:v2:key2' => ['value' => 'test2']]);

    expect(is_dir($cachePath->parallelDir()))->toBeTrue();
    expect(is_dir($cachePath->parallelCacheDir(123)))->toBeTrue();
    expect(is_dir($cachePath->parallelCacheDir(456)))->toBeTrue();

    CacheManager::mergeParallelCaches();

    expect(is_dir($cachePath->parallelDir()))->toBeFalse();

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('initializeParallelCache returns null when cache path is invalid', function () {
    CacheManager::setCachePath(new CachePath(''));

    expect(CacheManager::initializeParallelCache())->toBeNull();
});

test('initializeParallelCache returns path without seeding when main cache does not exist', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    $result = CacheManager::initializeParallelCache();

    expect($result)->not->toBeNull();
    expect($result)->toContain('parallel');
    expect(file_exists($cachePath->parallelCache(getmypid())))->toBeFalse();

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('initializeParallelCache seeds cache from main cache when it exists', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createMainCacheFile($cachePath, [
        'keyv:fetch:v2:existing-key' => ['expire' => 999, 'value' => 'existing-value'],
    ]);

    $result = CacheManager::initializeParallelCache();

    expect($result)->not->toBeNull();

    $parallelCacheFile = $cachePath->parallelCache(getmypid());
    expect(file_exists($parallelCacheFile))->toBeTrue();

    $parallelData = json_decode(file_get_contents($parallelCacheFile), true);
    $entries = array_column($parallelData['cache'], 1, 0);
    expect($entries)->toHaveKey('keyv:fetch:v2:existing-key');
    expect($entries['keyv:fetch:v2:existing-key']['value'])->toBe('existing-value');

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('initializeParallelCache skips seeding if parallel cache already exists', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    createMainCacheFile($cachePath, [
        'keyv:fetch:v2:main-key' => ['expire' => 111, 'value' => 'main-value'],
    ]);

    createParallelCache($cachePath, getmypid(), [
        'keyv:fetch:v2:parallel-key' => ['expire' => 222, 'value' => 'parallel-value'],
    ]);

    $result = CacheManager::initializeParallelCache();

    expect($result)->not->toBeNull();

    $parallelCacheFile = $cachePath->parallelCache(getmypid());
    $parallelData = json_decode(file_get_contents($parallelCacheFile), true);
    $entries = array_column($parallelData['cache'], 1, 0);

    expect($entries)->toHaveKey('keyv:fetch:v2:parallel-key');
    expect($entries)->not->toHaveKey('keyv:fetch:v2:main-key');

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});

test('reset clears cached state', function () {
    $cachePath = createTestCachePath();
    CacheManager::setCachePath($cachePath);

    CacheManager::reset();

    CacheManager::setCachePath(new CachePath(''));
    expect(CacheManager::initializeParallelCache())->toBeNull();

    deleteTestDirectory(dirname($cachePath->cacheDir()));
});
