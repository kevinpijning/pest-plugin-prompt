<?php

declare(strict_types=1);

use Pest\Prompt\Path;

test('withFileName creates a new Path instance', function () {
    $path = Path::withFileName('test-name');

    expect($path)->toBeInstanceOf(Path::class);
});

test('generate returns filename with default extension when no folder is set', function () {
    $result = Path::withFileName('test-name')->toString();

    expect($result)->toBe('test-name.html');
});

test('generate returns full path when folder is set', function () {
    $result = Path::withFileName('test-name')
        ->inFolder('/path/to/output')
        ->toString();

    expect($result)->toBe('/path/to/output/test-name.html');
});

test('inFolder handles folder path with trailing slash', function () {
    $result = Path::withFileName('test-name')
        ->inFolder('/path/to/output/')
        ->toString();

    expect($result)->toBe('/path/to/output/test-name.html');
});

test('inFolder handles null folder', function () {
    $result = Path::withFileName('test-name')
        ->inFolder(null)
        ->toString();

    expect($result)->toBe('test-name.html');
});

test('withExtension sets custom extension', function () {
    $result = Path::withFileName('test-name')
        ->withExtension('json')
        ->toString();

    expect($result)->toBe('test-name.json');
});

test('withExtension handles extension with leading dot', function () {
    $result = Path::withFileName('test-name')
        ->withExtension('.json')
        ->toString();

    expect($result)->toBe('test-name.json');
});

test('includeDatetime adds datetime prefix to filename', function () {
    $result = Path::withFileName('test-name')
        ->includeDatetime()
        ->toString();

    // Should match format: YYYY-MM-DD_HHMMSS_test-name.html
    expect($result)->toMatch('/^\d{4}-\d{2}-\d{2}_\d{6}_test-name\.html$/');
});

test('includeUniqueId adds unique ID suffix to filename', function () {
    $result = Path::withFileName('test-name')
        ->includeUniqueId()
        ->toString();

    // Should match format: test-name_<uniqueid>.html
    // uniqid with more_entropy returns: 14 hex chars + dot + 8 decimal digits
    expect($result)->toMatch('/^test-name_[a-f0-9]{14}\.[0-9]{8}\.html$/');
});

test('includeDatetime and includeUniqueId can be used together', function () {
    $result = Path::withFileName('test-name')
        ->includeDatetime()
        ->includeUniqueId()
        ->toString();

    // Should match format: YYYY-MM-DD_HHMMSS_test-name_<uniqueid>.html
    // uniqid with more_entropy returns: 14 hex chars + dot + 8 decimal digits
    expect($result)->toMatch('/^\d{4}-\d{2}-\d{2}_\d{6}_test-name_[a-f0-9]{14}\.[0-9]{8}\.html$/');
});

test('generate sanitizes special characters in filename', function () {
    $result = Path::withFileName('test (with) [special] chars!')
        ->toString();

    expect($result)->toBe('test_with_special_chars.html')
        ->and($result)->not->toContain('(')
        ->and($result)->not->toContain(')')
        ->and($result)->not->toContain('[')
        ->and($result)->not->toContain(']')
        ->and($result)->not->toContain('!')
        ->and($result)->not->toContain(' ');
});

test('generate converts name to lowercase', function () {
    $result = Path::withFileName('TEST-Name-With-Mixed-Case')
        ->toString();

    expect($result)->toBe('test-name-with-mixed-case.html');
});

test('generate removes Pest internal prefix', function () {
    $result = Path::withFileName('__pest_evaluable_test-name')
        ->toString();

    expect($result)->toBe('test-name.html');
});

test('generate collapses multiple underscores', function () {
    $result = Path::withFileName('test___name___with___underscores')
        ->toString();

    expect($result)->toBe('test_name_with_underscores.html');
});

test('generate trims leading and trailing underscores', function () {
    $result = Path::withFileName('___test-name___')
        ->toString();

    expect($result)->toBe('test-name.html');
});

test('generate handles empty sanitized name', function () {
    $result = Path::withFileName('!!!')
        ->toString();

    // Should still produce a valid filename, even if name becomes empty after sanitization
    expect($result)->toMatch('/^.*\.html$/');
});

test('fluent builder methods can be chained in any order', function () {
    $result1 = Path::withFileName('test')
        ->inFolder('/output')
        ->withExtension('json')
        ->includeDatetime()
        ->includeUniqueId()
        ->toString();

    $result2 = Path::withFileName('test')
        ->includeDatetime()
        ->includeUniqueId()
        ->withExtension('json')
        ->inFolder('/output')
        ->toString();

    // Both should produce valid paths with the same components
    // uniqid with more_entropy returns: 14 hex chars + dot + 8 decimal digits
    expect($result1)->toMatch('/^\/output\/\d{4}-\d{2}-\d{2}_\d{6}_test_[a-f0-9]{14}\.[0-9]{8}\.json$/')
        ->and($result2)->toMatch('/^\/output\/\d{4}-\d{2}-\d{2}_\d{6}_test_[a-f0-9]{14}\.[0-9]{8}\.json$/');
});

test('generate creates unique paths when includeUniqueId is used', function () {
    $paths = [];
    for ($i = 0; $i < 100; $i++) {
        $paths[] = Path::withFileName('test-name')
            ->includeUniqueId()
            ->toString();
    }

    // All paths should be unique
    $uniquePaths = array_unique($paths);
    expect(count($uniquePaths))->toBe(100);
});

test('generate creates unique paths with datetime and uniqueId in concurrent scenarios', function () {
    $paths = [];

    // Simulate rapid successive calls (as might happen in concurrent scenarios)
    for ($i = 0; $i < 50; $i++) {
        $paths[] = Path::withFileName('test-name')
            ->includeDatetime()
            ->includeUniqueId()
            ->toString();

        // Small delay to ensure we're in the same second (worst case scenario)
        usleep(1000); // 1ms delay
    }

    // All paths should be unique even when generated in the same second
    $uniquePaths = array_unique($paths);
    expect(count($uniquePaths))->toBe(50);
});

test('generate handles unicode and special characters', function () {
    $result = Path::withFileName('test-ñame-with-émojis-🚀')
        ->toString();

    // Unicode characters should be converted to underscores
    expect($result)->toMatch('/^test.*\.html$/')
        ->and($result)->not->toContain('ñ')
        ->and($result)->not->toContain('é')
        ->and($result)->not->toContain('🚀');
});

test('generate handles very long filenames', function () {
    $longName = str_repeat('a', 200);
    $result = Path::withFileName($longName)
        ->toString();

    expect($result)->toMatch('/^a+\.html$/');
});

test('generate handles folder with Windows-style path separators', function () {
    $result = Path::withFileName('test-name')
        ->inFolder('C:\\path\\to\\output')
        ->toString();

    // Should handle Windows paths correctly
    expect($result)->toContain('test-name.html');
});

test('generate with all options produces correct format', function () {
    $result = Path::withFileName('my-test')
        ->inFolder('/output/folder')
        ->withExtension('html')
        ->includeDatetime()
        ->includeUniqueId()
        ->toString();

    // Full format: /output/folder/YYYY-MM-DD_HHMMSS_my-test_<uniqueid>.html
    // uniqid with more_entropy returns: 14 hex chars + dot + 8 decimal digits
    expect($result)->toMatch('/^\/output\/folder\/\d{4}-\d{2}-\d{2}_\d{6}_my-test_[a-f0-9]{14}\.[0-9]{8}\.html$/');
});
