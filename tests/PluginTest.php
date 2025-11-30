<?php

declare(strict_types=1);

use Pest\Prompt\OutputPath;
use Pest\Prompt\Plugin;

beforeEach(function () {
    OutputPath::clear();
});

test('handle arguments throws exception when --output is provided without value', function () {
    $plugin = new Plugin;

    expect(fn () => $plugin->handleArguments(['script.php', '--output']))
        ->toThrow(InvalidArgumentException::class, 'The --output option requires a value');
});

test('handle arguments throws exception when --output is provided with empty value', function () {
    $plugin = new Plugin;

    expect(fn () => $plugin->handleArguments(['script.php', '--output', '']))
        ->toThrow(InvalidArgumentException::class, 'The --output option requires a value');
});

test('handle arguments sets output path when valid value is provided', function () {
    $plugin = new Plugin;

    $result = $plugin->handleArguments(['script.php', '--output', 'path/to/results.html']);

    expect(OutputPath::has())->toBeTrue()
        ->and(OutputPath::get())->toBe('path/to/results.html')
        ->and($result)->not->toContain('--output');
});

test('handle arguments sets output path when using equals syntax', function () {
    $plugin = new Plugin;

    $result = $plugin->handleArguments(['script.php', '--output=path/to/results.html']);

    expect(OutputPath::has())->toBeTrue()
        ->and(OutputPath::get())->toBe('path/to/results.html')
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
        ->and(OutputPath::has())->toBeFalse();
});
