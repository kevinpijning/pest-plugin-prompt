<?php

use KevinPijning\Prompt\Provider;
use KevinPijning\Prompt\ProviderFactory;

beforeEach(function () {
    Provider::flushExtensions();
});

test('provider() without arguments returns ProviderFactory', function () {
    expect(provider())->toBeInstanceOf(ProviderFactory::class);
});

test('provider() with name returns Provider', function () {
    expect(provider('test-provider'))->toBeInstanceOf(Provider::class);
});

test('extend registers an extension via factory', function () {
    provider()->extend('withApiKey', fn (string $key) => $this->config(['apiKey' => $key]));

    expect(Provider::hasExtension('withApiKey'))->toBeTrue();
});

test('extend returns factory for chaining', function () {
    $factory = provider();

    $result = $factory->extend('method1', fn () => $this);

    expect($result)->toBe($factory);
});

test('extend can chain multiple extensions', function () {
    provider()
        ->extend('withApiKey', fn (string $key) => $this->config(['apiKey' => $key]))
        ->extend('withBaseUrl', fn (string $url) => $this->mergeConfig(['baseUrl' => $url]));

    expect(Provider::hasExtension('withApiKey'))->toBeTrue()
        ->and(Provider::hasExtension('withBaseUrl'))->toBeTrue();
});

test('hasExtension returns true for registered extensions', function () {
    provider()->extend('customMethod', fn () => $this);

    expect(provider()->hasExtension('customMethod'))->toBeTrue();
});

test('hasExtension returns false for unregistered extensions', function () {
    expect(provider()->hasExtension('nonExistent'))->toBeFalse();
});

test('flushExtensions removes all registered extensions', function () {
    provider()
        ->extend('method1', fn () => $this)
        ->extend('method2', fn () => $this);

    expect(Provider::hasExtension('method1'))->toBeTrue()
        ->and(Provider::hasExtension('method2'))->toBeTrue();

    provider()->flushExtensions();

    expect(Provider::hasExtension('method1'))->toBeFalse()
        ->and(Provider::hasExtension('method2'))->toBeFalse();
});

test('extensions persist across multiple factory instances', function () {
    provider()->extend('globalMethod', fn () => $this);

    $newFactory = provider();

    expect($newFactory->hasExtension('globalMethod'))->toBeTrue();
});

test('id method returns a Provider with the given id', function () {
    $provider = provider()->id('openai:gpt-4');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and($provider->build()->id)->toBe('openai:gpt-4');
});

test('id method can be chained with provider configuration', function () {
    $provider = provider()
        ->id('openai:gpt-4')
        ->temperature(0.7)
        ->maxTokens(1000);

    $built = $provider->build();

    expect($built->id)->toBe('openai:gpt-4')
        ->and($built->temperature)->toBe(0.7)
        ->and($built->maxTokens)->toBe(1000);
});

test('id method works with registered extensions', function () {
    provider()->extend('withApiKey', fn (Provider $self, string $key) => $self->mergeConfig(['apiKey' => $key]));

    $provider = provider()
        ->id('openai:gpt-4')
        ->withApiKey('test-key');

    expect($provider->build()->config)->toBe(['apiKey' => 'test-key']);
});
