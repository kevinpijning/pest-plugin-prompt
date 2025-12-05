<?php

declare(strict_types=1);

use KevinPijning\Prompt\Output;

test('withName creates a new Output instance', function () {
    $output = Output::withName('test-name');

    expect($output)->toBeInstanceOf(Output::class);
});

test('toFolder sets the folder and returns new instance', function () {
    $output1 = Output::withName('test-name');
    $output2 = $output1->toFolder('/path/to/output');

    expect($output2)->not->toBe($output1);
});

test('toFolder can be chained', function () {
    $result = Output::withName('test-name')
        ->toFolder('/path/to/output')
        ->toFolder('/another/path')
        ->generate();

    expect($result)->toContain('/another/path');
});

test('extension sets the extension and returns new instance', function () {
    $output1 = Output::withName('test-name');
    $output2 = $output1->extension('json');

    expect($output2)->not->toBe($output1);
});

test('extension can be chained', function () {
    $result = Output::withName('test-name')
        ->extension('json')
        ->extension('html')
        ->generate();

    expect($result)->toEndWith('.html');
});

test('includeDatetime adds datetime prefix and returns new instance', function () {
    $output1 = Output::withName('test-name');
    $output2 = $output1->includeDatetime();

    expect($output2)->not->toBe($output1);
});

test('includeDatetime can be chained', function () {
    $result = Output::withName('test-name')
        ->includeDatetime()
        ->generate();

    // Should match format: YYYY-MM-DD_HHMMSS_test-name.html
    expect($result)->toMatch('/^\d{4}-\d{2}-\d{2}_\d{6}_test-name\.html$/');
});

test('includeUniqueId adds unique ID suffix and returns new instance', function () {
    $output1 = Output::withName('test-name');
    $output2 = $output1->includeUniqueId();

    expect($output2)->not->toBe($output1);
});

test('includeUniqueId can be chained', function () {
    $result = Output::withName('test-name')
        ->includeUniqueId()
        ->generate();

    // Should match format: test-name_<uniqueid>.html
    expect($result)->toMatch('/^test-name_[a-f0-9]{13}\.html$/');
});

test('includeDatetime and includeUniqueId can be used together', function () {
    $result = Output::withName('test-name')
        ->includeDatetime()
        ->includeUniqueId()
        ->generate();

    // Should match format: YYYY-MM-DD_HHMMSS_test-name_<uniqueid>.html
    expect($result)->toMatch('/^\d{4}-\d{2}-\d{2}_\d{6}_test-name_[a-f0-9]{13}\.html$/');
});

test('generate returns filename with default extension when no folder is set', function () {
    $result = Output::withName('test-name')->generate();

    expect($result)->toBe('test-name.html');
});

test('generate returns full path when folder is set', function () {
    $result = Output::withName('test-name')
        ->toFolder('/path/to/output')
        ->generate();

    expect($result)->toBe('/path/to/output/test-name.html');
});

test('generate handles folder path with trailing slash', function () {
    $result = Output::withName('test-name')
        ->toFolder('/path/to/output/')
        ->generate();

    expect($result)->toBe('/path/to/output/test-name.html');
});

test('generate handles null folder', function () {
    $result = Output::withName('test-name')
        ->toFolder(null)
        ->generate();

    expect($result)->toBe('test-name.html');
});

test('generate sanitizes special characters in filename', function () {
    $result = Output::withName('test (with) [special] chars!')
        ->generate();

    expect($result)->toBe('test_with_special_chars.html')
        ->and($result)->not->toContain('(')
        ->and($result)->not->toContain(')')
        ->and($result)->not->toContain('[')
        ->and($result)->not->toContain(']')
        ->and($result)->not->toContain('!')
        ->and($result)->not->toContain(' ');
});

test('generate converts name to lowercase', function () {
    $result = Output::withName('TEST-Name-With-Mixed-Case')
        ->generate();

    expect($result)->toBe('test-name-with-mixed-case.html');
});

test('generate removes Pest internal prefix', function () {
    $result = Output::withName('__pest_evaluable_test-name')
        ->generate();

    expect($result)->toBe('test-name.html');
});

test('generate collapses multiple underscores', function () {
    $result = Output::withName('test___name___with___underscores')
        ->generate();

    expect($result)->toBe('test_name_with_underscores.html');
});

test('generate trims leading and trailing underscores', function () {
    $result = Output::withName('___test-name___')
        ->generate();

    expect($result)->toBe('test-name.html');
});

test('generate handles empty sanitized name', function () {
    $result = Output::withName('!!!')
        ->generate();

    // Should still produce a valid filename, even if name becomes empty after sanitization
    expect($result)->toMatch('/^.*\.html$/');
});

test('fluent builder methods can be chained in any order', function () {
    $result1 = Output::withName('test')
        ->toFolder('/output')
        ->extension('json')
        ->includeDatetime()
        ->includeUniqueId()
        ->generate();

    $result2 = Output::withName('test')
        ->includeDatetime()
        ->includeUniqueId()
        ->extension('json')
        ->toFolder('/output')
        ->generate();

    // Both should produce valid paths with the same components
    expect($result1)->toMatch('/^\/output\/\d{4}-\d{2}-\d{2}_\d{6}_test_[a-f0-9]{13}\.json$/')
        ->and($result2)->toMatch('/^\/output\/\d{4}-\d{2}-\d{2}_\d{6}_test_[a-f0-9]{13}\.json$/');
});

test('generate creates unique paths when includeUniqueId is used', function () {
    $paths = [];
    for ($i = 0; $i < 100; $i++) {
        $paths[] = Output::withName('test-name')
            ->includeUniqueId()
            ->generate();
    }

    // All paths should be unique
    $uniquePaths = array_unique($paths);
    expect(count($uniquePaths))->toBe(100);
});
