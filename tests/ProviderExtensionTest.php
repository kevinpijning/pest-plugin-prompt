<?php

use KevinPijning\Prompt\Provider;

beforeEach(function () {
    Provider::flushExtensions();
});

test('extension method can be called on provider instance', function () {
    Provider::extend('withApiKey', fn (Provider $self, string $key) => $self->config(['apiKey' => $key]));

    $provider = Provider::create('openai:gpt-4')
        ->withApiKey('test-key');

    expect($provider->build()->config)->toBe(['apiKey' => 'test-key']);
});

test('extension method returns provider for chaining', function () {
    Provider::extend('withApiKey', fn (Provider $self, string $key) => $self->config(['apiKey' => $key]));

    $provider = new Provider;
    $result = $provider->withApiKey('test-key');

    expect($result)->toBe($provider);
});

test('extension method can access provider properties via $this', function () {
    Provider::extend('withVectorStore', fn (Provider $self, string $id) => $self->mergeConfig([
        'tools' => [['type' => 'file_search']],
        'tool_resources' => ['file_search' => ['vector_store_ids' => [$id]]],
    ]));

    $provider = Provider::create('openai:responses:gpt-4o-mini')
        ->config(['existing' => 'value'])
        ->withVectorStore('vs_abc123');

    $config = $provider->build()->config;

    expect($config)->toBe([
        'existing' => 'value',
        'tools' => [['type' => 'file_search']],
        'tool_resources' => ['file_search' => ['vector_store_ids' => ['vs_abc123']]],
    ]);
});

test('extension method can accept multiple arguments', function () {
    Provider::extend('withAuth', fn (Provider $self, string $key, string $org) => $self->config([
        'apiKey' => $key,
        'organization' => $org,
    ]));

    $provider = Provider::create('openai:gpt-4')
        ->withAuth('key-123', 'org-456');

    expect($provider->build()->config)->toBe([
        'apiKey' => 'key-123',
        'organization' => 'org-456',
    ]);
});

test('extension method can use closure with existing config', function () {
    Provider::extend('addHeader', fn (Provider $self, string $name, string $value) => $self->config(
        fn (array $config) => [
            ...$config,
            'headers' => [...($config['headers'] ?? []), $name => $value],
        ]
    ));

    $provider = Provider::create('openai:gpt-4')
        ->config(['baseUrl' => 'https://api.example.com'])
        ->addHeader('X-Custom', 'value1')
        ->addHeader('X-Another', 'value2');

    expect($provider->build()->config)->toBe([
        'baseUrl' => 'https://api.example.com',
        'headers' => [
            'X-Custom' => 'value1',
            'X-Another' => 'value2',
        ],
    ]);
});

test('extension without return implicitly returns provider', function () {
    Provider::extend('setLabel', function (Provider $self, string $label): void {
        $self->label($label);
    });

    $provider = new Provider;
    $result = $provider->setLabel('Test Label');

    expect($result)->toBe($provider)
        ->and($provider->build()->label)->toBe('Test Label');
});

test('calling undefined extension throws BadMethodCallException', function () {
    $provider = new Provider;

    $provider->undefinedMethod();
})->throws(BadMethodCallException::class, 'Method KevinPijning\Prompt\Provider::undefinedMethod does not exist.');

test('extensions can be chained with built-in methods', function () {
    Provider::extend('withApiKey', fn (Provider $self, string $key) => $self->mergeConfig(['apiKey' => $key]));

    $provider = Provider::create('openai:gpt-4')
        ->temperature(0.7)
        ->withApiKey('test-key')
        ->maxTokens(1000)
        ->label('Custom Provider');

    $built = $provider->build();

    expect($built->id)->toBe('openai:gpt-4')
        ->and($built->temperature)->toBe(0.7)
        ->and($built->maxTokens)->toBe(1000)
        ->and($built->label)->toBe('Custom Provider')
        ->and($built->config)->toBe(['apiKey' => 'test-key']);
});

test('extensions work with globally registered providers', function () {
    Provider::extend('withApiKey', fn (Provider $self, string $key) => $self->mergeConfig(['apiKey' => $key]));

    $provider = provider('my-provider')
        ->id('openai:gpt-4')
        ->withApiKey('global-key');

    expect($provider->build()->config)->toBe(['apiKey' => 'global-key']);
});

test('extension method can return custom value', function () {
    Provider::extend('getConfigValue', fn (Provider $self, string $key) => $self->build()->config[$key] ?? null);

    $provider = Provider::create('openai:gpt-4')
        ->config(['apiKey' => 'secret-key']);

    $value = $provider->getConfigValue('apiKey');

    expect($value)->toBe('secret-key');
});
