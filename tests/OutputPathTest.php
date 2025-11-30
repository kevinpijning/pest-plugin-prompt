<?php

declare(strict_types=1);

use Pest\Prompt\OutputPath;

beforeEach(function () {
    OutputPath::clear();
});

test('has returns false by default', function () {
    expect(OutputPath::has())->toBeFalse();
});

test('get returns null by default', function () {
    expect(OutputPath::get())->toBeNull();
});

test('set sets the path', function () {
    $path = '/path/to/output.html';

    OutputPath::set($path);

    expect(OutputPath::has())->toBeTrue()
        ->and(OutputPath::get())->toBe($path);
});

test('clear resets the output path', function () {
    OutputPath::set('/path/to/output.html');

    OutputPath::clear();

    expect(OutputPath::has())->toBeFalse()
        ->and(OutputPath::get())->toBeNull();
});

test('set overwrites previous path', function () {
    OutputPath::set('/first/path.html');

    OutputPath::set('/second/path.html');

    expect(OutputPath::get())->toBe('/second/path.html');
});

test('generate sanitizes special characters: (test) with [special] chars!', function () {
    $result = OutputPath::generate('output');

    // Should sanitize special characters from test name (spaces, colons, parentheses, brackets become underscores, like Pest does)
    // The exact path format: output/datetime_sanitized_test_name.html
    // Special characters are converted to underscores to match Pest's internal format
    expect($result)->toMatch('/^output\/\d{4}_\d{2}_\d{2}_\d{2}_\d{2}_\d{2}_generate_sanitizes_special_characters_test_with_special_chars\.html$/')
        ->and($result)->not->toContain('(')
        ->and($result)->not->toContain(')')
        ->and($result)->not->toContain('[')
        ->and($result)->not->toContain(']')
        ->and($result)->not->toContain(':')
        ->and($result)->not->toContain(' ');
});
