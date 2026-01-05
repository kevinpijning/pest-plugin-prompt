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
