<?php

declare(strict_types=1);

use KevinPijning\Prompt\Promptfoo\Promptfoo;

beforeEach(function () {
    // Reset to defaults before each test
    Promptfoo::setDefaultProviders(['openai:gpt-4o-mini']);
    Promptfoo::setOutputFolder(null);
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

test('initialize returns a PromptfooClient instance', function () {
    $client = Promptfoo::initialize();

    expect($client)->toBeInstanceOf(\KevinPijning\Prompt\Promptfoo\PromptfooClient::class);
});
