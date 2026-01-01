<?php

declare(strict_types=1);

use KevinPijning\Prompt\Internal\ProviderContext;
use KevinPijning\Prompt\Provider;

beforeEach(function () {
    // Nothing to clear explicitly; contexts are static and tests are simple.
});

test('PromptProviderContext can add and retrieve a provider', function () {
    $provider = Provider::create('openai:gpt-4');

    $result = ProviderContext::add('openai', $provider);

    expect($result)->toBe($provider)
        ->and(ProviderContext::has('openai'))->toBeTrue()
        ->and(ProviderContext::get('openai'))->toBe($provider);
});

test('PromptProviderContext can store multiple providers with different names', function () {
    $provider1 = Provider::create('openai:gpt-4');
    $provider2 = Provider::create('anthropic:claude-3');

    ProviderContext::add('openai', $provider1);
    ProviderContext::add('anthropic', $provider2);

    expect(ProviderContext::has('openai'))->toBeTrue()
        ->and(ProviderContext::has('anthropic'))->toBeTrue()
        ->and(ProviderContext::get('openai'))->toBe($provider1)
        ->and(ProviderContext::get('anthropic'))->toBe($provider2);
});

test('PromptProviderContext overwrites providers with the same name', function () {
    $provider1 = Provider::create('openai:gpt-4');
    $provider2 = Provider::create('anthropic:claude-3');

    ProviderContext::add('my-provider', $provider1);
    expect(ProviderContext::get('my-provider'))->toBe($provider1);

    ProviderContext::add('my-provider', $provider2);

    expect(ProviderContext::get('my-provider'))->toBe($provider2)
        ->and(ProviderContext::get('my-provider'))->not->toBe($provider1);
});
