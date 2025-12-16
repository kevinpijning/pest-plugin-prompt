<?php

declare(strict_types=1);

use KevinPijning\Prompt\Internal\TestContext;
use KevinPijning\Prompt\Provider;

beforeEach(function () {
    TestContext::clear();
});

test('provider function creates and registers a provider without config', function () {
    $provider = provider('my-provider');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and(TestContext::hasProvider('my-provider'))->toBeTrue()
        ->and(TestContext::getProvider('my-provider'))->toBe($provider);
});

test('provider function creates and registers a provider with config callable', function () {
    $provider = provider('my-provider', function (Provider $p) {
        return $p->id('openai:gpt-4')
            ->label('Custom Provider')
            ->temperature(0.7)
            ->maxTokens(2000);
    });

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and(TestContext::hasProvider('my-provider'))->toBeTrue()
        ->and(TestContext::getProvider('my-provider'))->toBe($provider)
        ->and($provider->build()->id)->toBe('openai:gpt-4')
        ->and($provider->build()->label)->toBe('Custom Provider')
        ->and($provider->build()->temperature)->toBe(0.7)
        ->and($provider->build()->maxTokens)->toBe(2000);
});

test('provider function can register multiple providers with different names', function () {
    $provider1 = provider('openai', fn (Provider $p) => $p->id('openai:gpt-4'));
    $provider2 = provider('anthropic', fn (Provider $p) => $p->id('anthropic:claude-3'));
    $provider3 = provider('google', fn (Provider $p) => $p->id('google:gemini'));

    expect(TestContext::hasProvider('openai'))->toBeTrue()
        ->and(TestContext::hasProvider('anthropic'))->toBeTrue()
        ->and(TestContext::hasProvider('google'))->toBeTrue()
        ->and(TestContext::getProvider('openai'))->toBe($provider1)
        ->and(TestContext::getProvider('anthropic'))->toBe($provider2)
        ->and(TestContext::getProvider('google'))->toBe($provider3);
});

test('provider function overwrites existing provider with same name', function () {
    $provider1 = provider('my-provider', fn (Provider $p) => $p->id('openai:gpt-4'));
    $provider2 = provider('my-provider', fn (Provider $p) => $p->id('anthropic:claude-3'));

    expect(TestContext::getProvider('my-provider'))->toBe($provider2)
        ->and(TestContext::getProvider('my-provider'))->not->toBe($provider1)
        ->and($provider2->build()->id)->toBe('anthropic:claude-3');
});

test('provider function returns the same instance that is stored in TestContext', function () {
    $provider = provider('my-provider');

    $stored = TestContext::getProvider('my-provider');

    expect($provider)->toBe($stored);
});
