<?php

declare(strict_types=1);

use KevinPijning\Prompt\Promptfoo\Promptfoo;
use KevinPijning\Prompt\Promptfoo\PromptfooConfiguration;

beforeEach(function () {
    Promptfoo::reset();
});

test('defaultProviders returns the default providers', function () {
    $providers = Promptfoo::defaultProviders();

    expect($providers)->toBe(['openai:gpt-4o-mini']);
});

test('setDefaultProviders sets the default providers', function () {
    Promptfoo::setDefaultProviders(['anthropic:claude-3', 'google:gemini']);

    expect(Promptfoo::defaultProviders())->toBe(['anthropic:claude-3', 'google:gemini']);
});

test('setOutputFolder sets the output folder', function () {
    Promptfoo::setOutputFolder('/custom/output/path');

    expect(Promptfoo::outputFolder())->toBe('/custom/output/path');
});

test('setOutputFolder with null clears the output folder', function () {
    Promptfoo::setOutputFolder('/custom/output/path');
    Promptfoo::setOutputFolder(null);

    expect(Promptfoo::outputFolder())->toBeNull();
});

test('outputFolder returns the output folder', function () {
    Promptfoo::setOutputFolder('/test/path');

    expect(Promptfoo::outputFolder())->toBe('/test/path');
});

test('shouldOutput returns true when output folder is set', function () {
    Promptfoo::setOutputFolder('/test/path');

    expect(Promptfoo::shouldOutput())->toBeTrue();
});

test('shouldOutput returns false when output folder is null', function () {
    Promptfoo::setOutputFolder(null);

    expect(Promptfoo::shouldOutput())->toBeFalse();
});

test('configuration returns a PromptfooConfiguration instance', function () {
    $config = Promptfoo::configuration();

    expect($config)->toBeInstanceOf(PromptfooConfiguration::class);
});

test('configuration returns the same instance on multiple calls', function () {
    $config1 = Promptfoo::configuration();
    $config2 = Promptfoo::configuration();

    expect($config1)->toBe($config2);
});

test('reset clears the configuration', function () {
    Promptfoo::setDefaultProviders(['custom-provider']);
    Promptfoo::setOutputFolder('/custom/path');

    Promptfoo::reset();

    expect(Promptfoo::defaultProviders())->toBe(['openai:gpt-4o-mini'])
        ->and(Promptfoo::outputFolder())->toBeNull();
});
