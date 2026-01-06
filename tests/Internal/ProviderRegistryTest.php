<?php

declare(strict_types=1);

use KevinPijning\Prompt\Internal\ProviderRegistry;
use KevinPijning\Prompt\Provider;

beforeEach(function () {
    // Nothing to clear explicitly; contexts are static and tests are simple.
});

test('ProviderRegistry can add and retrieve a provider', function () {
    $provider = provider();

    $result = ProviderRegistry::add('openai', $provider);

    expect($result)->toBe($provider)
        ->and(ProviderRegistry::has('openai'))->toBeTrue()
        ->and(ProviderRegistry::get('openai'))->toBe($provider);
});

test('ProviderRegistry can store multiple providers with different names', function () {
    $provider1 = provider()->id('openai:gpt-4');
    $provider2 = Provider::create('anthropic:claude-3');

    ProviderRegistry::add('openai', $provider1);
    ProviderRegistry::add('anthropic', $provider2);

    expect(ProviderRegistry::has('openai'))->toBeTrue()
        ->and(ProviderRegistry::has('anthropic'))->toBeTrue()
        ->and(ProviderRegistry::get('openai'))->toBe($provider1)
        ->and(ProviderRegistry::get('anthropic'))->toBe($provider2);
});

test('ProviderRegistry overwrites providers with the same name', function () {
    $provider1 = provider()->id('openai:gpt-4');
    $provider2 = Provider::create('anthropic:claude-3');

    ProviderRegistry::add('my-provider', $provider1);
    expect(ProviderRegistry::get('my-provider'))->toBe($provider1);

    ProviderRegistry::add('my-provider', $provider2);

    expect(ProviderRegistry::get('my-provider'))->toBe($provider2)
        ->and(ProviderRegistry::get('my-provider'))->not->toBe($provider1);
});
