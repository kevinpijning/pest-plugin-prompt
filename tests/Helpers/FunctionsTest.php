<?php

declare(strict_types=1);

use KevinPijning\Prompt\AssertionGroup;
use KevinPijning\Prompt\Internal\AssertionGroupContext;
use KevinPijning\Prompt\Internal\EvaluationRegistry;
use KevinPijning\Prompt\Internal\ProviderContext;
use KevinPijning\Prompt\Provider;
use KevinPijning\Prompt\TestCase;

beforeEach(function () {
    EvaluationRegistry::clear();
});

test('provider function creates and registers a provider without config', function () {
    $provider = provider('my-provider');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and(ProviderContext::has('my-provider'))->toBeTrue()
        ->and(ProviderContext::get('my-provider'))->toBe($provider);
});

test('provider function creates and registers a provider with config callable', function () {
    $provider = provider('my-provider', function (Provider $p) {
        return $p->id('openai:gpt-4')
            ->label('Custom Provider')
            ->temperature(0.7)
            ->maxTokens(2000);
    });

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and(ProviderContext::has('my-provider'))->toBeTrue()
        ->and(ProviderContext::get('my-provider'))->toBe($provider)
        ->and($provider->build()->id)->toBe('openai:gpt-4')
        ->and($provider->build()->label)->toBe('Custom Provider')
        ->and($provider->build()->temperature)->toBe(0.7)
        ->and($provider->build()->maxTokens)->toBe(2000);
});

test('provider function can register multiple providers with different names', function () {
    $provider1 = provider('openai', fn (Provider $p) => $p->id('openai:gpt-4'));
    $provider2 = provider('anthropic', fn (Provider $p) => $p->id('anthropic:claude-3'));
    $provider3 = provider('google', fn (Provider $p) => $p->id('google:gemini'));

    expect(ProviderContext::has('openai'))->toBeTrue()
        ->and(ProviderContext::has('anthropic'))->toBeTrue()
        ->and(ProviderContext::has('google'))->toBeTrue()
        ->and(ProviderContext::get('openai'))->toBe($provider1)
        ->and(ProviderContext::get('anthropic'))->toBe($provider2)
        ->and(ProviderContext::get('google'))->toBe($provider3);
});

test('provider function overwrites existing provider with same name', function () {
    $provider1 = provider('my-provider', fn (Provider $p) => $p->id('openai:gpt-4'));
    $provider2 = provider('my-provider', fn (Provider $p) => $p->id('anthropic:claude-3'));

    expect(ProviderContext::get('my-provider'))->toBe($provider2)
        ->and(ProviderContext::get('my-provider'))->not->toBe($provider1)
        ->and($provider2->build()->id)->toBe('anthropic:claude-3');
});

test('provider function returns the same instance that is stored in TestContext', function () {
    $provider = provider('my-provider');

    $stored = ProviderContext::get('my-provider');

    expect($provider)->toBe($stored);
});

test('assertion function creates and registers an assertion group without callback', function () {
    $group = assertion('be nice');

    expect($group)->toBeInstanceOf(AssertionGroup::class)
        ->and(AssertionGroupContext::has('be nice'))->toBeTrue()
        ->and(AssertionGroupContext::get('be nice'))->toBe($group);
});

test('assertion function creates and registers an assertion group with callback', function () {
    $callback = function (TestCase $tc): void {
        $tc->toContain('hello')
            ->toBeJudged('friendly');
    };

    $group = assertion('be nice', $callback);

    expect($group)->toBeInstanceOf(AssertionGroup::class)
        ->and(AssertionGroupContext::has('be nice'))->toBeTrue()
        ->and(AssertionGroupContext::get('be nice'))->toBe($group);
});

test('assertion function overwrites existing assertion group with same name', function () {
    $group1 = assertion('be nice');
    $group2 = assertion('be nice', function (TestCase $tc): void {
        $tc->toContain('hello');
    });

    expect(AssertionGroupContext::get('be nice'))->toBe($group2)
        ->and(AssertionGroupContext::get('be nice'))->not->toBe($group1);
});

test('assertion function returns the same instance that is stored in TestContext', function () {
    $group = assertion('be nice');

    $stored = AssertionGroupContext::get('be nice');

    expect($group)->toBe($stored);
});
