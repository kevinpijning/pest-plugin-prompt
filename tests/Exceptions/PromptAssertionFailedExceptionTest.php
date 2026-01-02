<?php

declare(strict_types=1);

use KevinPijning\Prompt\Exceptions\PromptAssertionFailedException;
use KevinPijning\Prompt\Helpers\SourceLocation;

test('PromptAssertionFailedException can be instantiated with SourceLocation and message', function () {
    $location = new SourceLocation('/path/to/file.php', 42);
    $exception = new PromptAssertionFailedException($location, 'Test error message');

    expect($exception->getMessage())->toBe('Test error message')
        ->and($exception->getSourceLocation())->toBe($location);
});

test('toCollisionEditor returns Frame with correct file and line', function () {
    $location = new SourceLocation('/path/to/test.php', 123);
    $exception = new PromptAssertionFailedException($location, 'Error');

    $frame = $exception->toCollisionEditor();

    expect($frame->getFile())->toBe('/path/to/test.php')
        ->and($frame->getLine())->toBe(123);
});

test('getSourceLocation returns the provided SourceLocation', function () {
    $location = new SourceLocation('/another/path.php', 99);
    $exception = new PromptAssertionFailedException($location, 'Message');

    expect($exception->getSourceLocation())->toBeInstanceOf(SourceLocation::class)
        ->and($exception->getSourceLocation()->file)->toBe('/another/path.php')
        ->and($exception->getSourceLocation()->line)->toBe(99);
});

test('PromptAssertionFailedException extends AssertionFailedError', function () {
    $location = new SourceLocation('/path/to/file.php', 1);
    $exception = new PromptAssertionFailedException($location, 'Error');

    expect($exception)->toBeInstanceOf(PHPUnit\Framework\AssertionFailedError::class);
});
