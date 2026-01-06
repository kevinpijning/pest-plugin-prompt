<?php

declare(strict_types=1);

use KevinPijning\Prompt\Helpers\SourceLocation;

test('constructor sets file and line properties', function () {
    $location = new SourceLocation('/path/to/test.php', 42);

    expect($location->file)->toBe('/path/to/test.php')
        ->and($location->line)->toBe(42);
});

test('isValid returns true for valid file and line', function () {
    $location = new SourceLocation('/path/to/test.php', 42);

    expect($location->isValid())->toBeTrue();
});

test('isValid returns false for empty file', function () {
    $location = new SourceLocation('', 42);

    expect($location->isValid())->toBeFalse();
});

test('isValid returns false for line zero', function () {
    $location = new SourceLocation('/path/to/test.php', 0);

    expect($location->isValid())->toBeFalse();
});

test('isValid returns false for negative line', function () {
    $location = new SourceLocation('/path/to/test.php', -1);

    expect($location->isValid())->toBeFalse();
});

test('capture returns SourceLocation from call stack', function () {
    $location = SourceLocation::capture();

    expect($location)->toBeInstanceOf(SourceLocation::class)
        ->and($location->file)->toContain('SourceLocationTest.php')
        ->and($location->line)->toBeGreaterThan(0);
});

test('fromBacktrace returns first non-filtered frame', function () {
    $backtrace = [
        ['file' => '/path/to/project/tests/MyTest.php', 'line' => 42],
    ];

    $location = SourceLocation::fromBacktrace($backtrace);

    expect($location)->toBeInstanceOf(SourceLocation::class)
        ->and($location->file)->toBe('/path/to/project/tests/MyTest.php')
        ->and($location->line)->toBe(42);
});

test('fromBacktrace skips plugin src frames but not test frames', function () {
    $backtrace = [
        ['file' => '/path/to/pest-plugin-prompt/tests/MyTest.php', 'line' => 42],
    ];

    $location = SourceLocation::fromBacktrace($backtrace);

    expect($location->file)->toBe('/path/to/pest-plugin-prompt/tests/MyTest.php');
});

test('fromBacktrace skips frames without file or line', function () {
    $backtrace = [
        ['function' => 'someFunction'],
        ['file' => '/path/to/project/tests/MyTest.php', 'line' => 42],
    ];

    $location = SourceLocation::fromBacktrace($backtrace);

    expect($location->file)->toBe('/path/to/project/tests/MyTest.php');
});

test('fromBacktrace returns null for empty backtrace', function () {
    $location = SourceLocation::fromBacktrace([]);

    expect($location)->toBeNull();
});
