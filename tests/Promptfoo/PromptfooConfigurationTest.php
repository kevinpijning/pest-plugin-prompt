<?php

declare(strict_types=1);

use KevinPijning\Prompt\Promptfoo\PromptfooConfiguration;

test('it has default values', function () {
    $config = new PromptfooConfiguration;

    expect($config->command())->toBe('npx promptfoo@latest')
        ->and($config->timeout())->toBe(300)
        ->and($config->defaultProviders())->toBe(['openai:gpt-4o-mini'])
        ->and($config->outputFolder())->toBeNull();
});

test('it can be constructed with custom values', function () {
    $config = new PromptfooConfiguration(
        command: 'custom-command',
        timeout: 600,
        defaultProviders: ['anthropic:claude-3'],
        outputFolder: '/custom/path',
    );

    expect($config->command())->toBe('custom-command')
        ->and($config->timeout())->toBe(600)
        ->and($config->defaultProviders())->toBe(['anthropic:claude-3'])
        ->and($config->outputFolder())->toBe('/custom/path');
});

test('withCommand returns new instance with updated command', function () {
    $original = new PromptfooConfiguration;
    $modified = $original->withCommand('new-command');

    expect($modified)->not->toBe($original)
        ->and($modified->command())->toBe('new-command')
        ->and($original->command())->toBe('npx promptfoo@latest');
});

test('withTimeout returns new instance with updated timeout', function () {
    $original = new PromptfooConfiguration;
    $modified = $original->withTimeout(600);

    expect($modified)->not->toBe($original)
        ->and($modified->timeout())->toBe(600)
        ->and($original->timeout())->toBe(300);
});

test('withDefaultProviders returns new instance with updated providers', function () {
    $original = new PromptfooConfiguration;
    $modified = $original->withDefaultProviders(['anthropic:claude-3', 'google:gemini']);

    expect($modified)->not->toBe($original)
        ->and($modified->defaultProviders())->toBe(['anthropic:claude-3', 'google:gemini'])
        ->and($original->defaultProviders())->toBe(['openai:gpt-4o-mini']);
});

test('withOutputFolder returns new instance with updated output folder', function () {
    $original = new PromptfooConfiguration;
    $modified = $original->withOutputFolder('/test/path');

    expect($modified)->not->toBe($original)
        ->and($modified->outputFolder())->toBe('/test/path')
        ->and($original->outputFolder())->toBeNull();
});

test('withOutputFolder can set null to clear output folder', function () {
    $original = new PromptfooConfiguration(outputFolder: '/test/path');
    $modified = $original->withOutputFolder(null);

    expect($modified->outputFolder())->toBeNull()
        ->and($original->outputFolder())->toBe('/test/path');
});

test('shouldOutput returns true when output folder is set', function () {
    $config = new PromptfooConfiguration(outputFolder: '/test/path');

    expect($config->shouldOutput())->toBeTrue();
});

test('shouldOutput returns false when output folder is null', function () {
    $config = new PromptfooConfiguration;

    expect($config->shouldOutput())->toBeFalse();
});

test('immutable setters preserve other values', function () {
    $original = new PromptfooConfiguration(
        command: 'original-command',
        timeout: 100,
        defaultProviders: ['provider-1'],
        outputFolder: '/original/path',
    );

    $modified = $original->withCommand('new-command');

    expect($modified->command())->toBe('new-command')
        ->and($modified->timeout())->toBe(100)
        ->and($modified->defaultProviders())->toBe(['provider-1'])
        ->and($modified->outputFolder())->toBe('/original/path');
});

test('immutable setters can be chained', function () {
    $config = (new PromptfooConfiguration)
        ->withCommand('chained-command')
        ->withTimeout(999)
        ->withDefaultProviders(['chained-provider'])
        ->withOutputFolder('/chained/path');

    expect($config->command())->toBe('chained-command')
        ->and($config->timeout())->toBe(999)
        ->and($config->defaultProviders())->toBe(['chained-provider'])
        ->and($config->outputFolder())->toBe('/chained/path');
});
