<?php

declare(strict_types=1);

use KevinPijning\Prompt\Exceptions\ExecutionException;

test('ExecutionException can be instantiated with message, command, output, and exit code', function () {
    $exception = new ExecutionException(
        'Test error message',
        'test-command',
        'test output',
        42
    );

    expect($exception->getMessage())->toBe('Test error message')
        ->and($exception->getCommand())->toBe('test-command')
        ->and($exception->getOutput())->toBe('test output')
        ->and($exception->getExitCode())->toBe(42);
});

test('ExecutionException can be instantiated with default output and exit code', function () {
    $exception = new ExecutionException(
        'Test error message',
        'test-command'
    );

    expect($exception->getMessage())->toBe('Test error message')
        ->and($exception->getCommand())->toBe('test-command')
        ->and($exception->getOutput())->toBe('')
        ->and($exception->getExitCode())->toBe(1);
});
