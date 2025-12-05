<?php

declare(strict_types=1);

use KevinPijning\Prompt\Plugin;
use KevinPijning\Prompt\Promptfoo\Promptfoo;

beforeEach(function () {
    // Reset output folder before each test
    Promptfoo::setOutputFolder(null);
});

test('handle arguments uses default path when --output is provided without value', function () {
    $plugin = new Plugin;

    $result = $plugin->handleArguments(['script.php', '--output']);

    expect(Promptfoo::shouldOutput())->toBeTrue()
        ->and(Promptfoo::outputFolder())->toBe('prompt-tests-output')
        ->and($result)->not->toContain('--output');
});

test('handle arguments sets output path when valid folder is provided', function () {
    $plugin = new Plugin;

    $result = $plugin->handleArguments(['script.php', '--output', 'custom-folder']);

    expect(Promptfoo::shouldOutput())->toBeTrue()
        ->and(Promptfoo::outputFolder())->toBe('custom-folder')
        ->and($result)->not->toContain('--output');
});

test('handle arguments sets output path when using equals syntax', function () {
    $plugin = new Plugin;

    $result = $plugin->handleArguments(['script.php', '--output=custom-folder']);

    expect(Promptfoo::shouldOutput())->toBeTrue()
        ->and(Promptfoo::outputFolder())->toBe('custom-folder')
        ->and($result)->not->toContain('--output');
});

test('handle arguments removes output value from arguments array', function () {
    $plugin = new Plugin;

    $result = $plugin->handleArguments(['script.php', '--output', 'test-output', 'other-arg']);

    expect($result)->toBe(['script.php', 'other-arg'])
        ->and($result)->not->toContain('test-output')
        ->and($result)->not->toContain('--output');
});

test('handle arguments returns arguments unchanged when --output is not provided', function () {
    $plugin = new Plugin;

    $arguments = ['script.php', '--other-option', 'value'];
    $result = $plugin->handleArguments($arguments);

    expect($result)->toBe($arguments)
        ->and(Promptfoo::shouldOutput())->toBeFalse();
});

test('in method returns correct path', function () {
    $plugin = new Plugin;
    $reflection = new ReflectionClass($plugin);
    $method = $reflection->getMethod('in');
    $method->setAccessible(true);

    $result = $method->invoke($plugin);

    // Should return a path that includes the test path
    expect($result)->toBeString()
        ->and($result)->not->toBeEmpty();
});
