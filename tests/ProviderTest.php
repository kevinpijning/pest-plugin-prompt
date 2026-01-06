<?php

use KevinPijning\Prompt\Provider;

test('a complete provider object', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->label('Custom label')
        ->maxTokens(1234)
        ->topP(.1)
        ->frequencyPenalty(.2)
        ->presencePenalty(.3)
        ->stop(['\n', 'Human:', 'AI:']);

    $built = $provider->build();
    expect($provider)->toBeInstanceOf(Provider::class)
        ->and($built->label)->toBe('Custom label')
        ->and($built->maxTokens)->toBe(1234)
        ->and($built->topP)->toBe(.1)
        ->and($built->frequencyPenalty)->toBe(.2)
        ->and($built->presencePenalty)->toBe(.3)
        ->and($built->stop)->toBe(['\n', 'Human:', 'AI:']);
});

test('the provider accepts a provider id', function () {
    $provider = Provider::create('openai:gpt-4o-mini');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and($provider->build()->id)->toBe('openai:gpt-4o-mini');
});

test('a label can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->label('custom label');

    expect($provider->build()->label
    )->toBe('custom label');
});

test('the temperature can be ser', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->temperature(.3);

    expect($provider->build()->temperature)->toBe(.3);
});

test('the max token can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->maxTokens(3);
    expect($provider->build()->maxTokens)->toBe(3);
});

test('the top p can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->topP(.8);
    expect($provider->build()->topP)->toBe(.8);
});

test('the frequency penalty can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->frequencyPenalty(.1);

    expect($provider->build()->frequencyPenalty)->toBe(.1);
});

test('the presence penalty can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->presencePenalty(.2);

    expect($provider->build()->presencePenalty)->toBe(.2);
});

test('custom config can be set with array', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->config([
            'apiKey' => 'fake-api-key',
        ]);

    expect($provider->build()->config)->toBe(['apiKey' => 'fake-api-key']);
});

test('custom config can be set with closure', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->config(fn (array $config) => [...$config, 'apiKey' => 'fake-api-key']);

    expect($provider->build()->config)->toBe(['apiKey' => 'fake-api-key']);
});

test('config closure receives current config and can modify it', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->config(['existing' => 'value'])
        ->config(fn (array $config) => [...$config, 'new' => 'added']);

    expect($provider->build()->config)->toBe([
        'existing' => 'value',
        'new' => 'added',
    ]);
});

test('config closure can completely replace config', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->config(['original' => 'value'])
        ->config(fn (array $config) => ['replaced' => 'entirely']);

    expect($provider->build()->config)->toBe(['replaced' => 'entirely']);
});

test('config array replaces previous config entirely', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->config(['first' => 'value'])
        ->config(['second' => 'value']);

    expect($provider->build()->config)->toBe(['second' => 'value']);
});

test('id method sets the provider id and returns self', function () {
    $provider = new Provider;

    $result = $provider->id('openai:gpt-4');

    expect($result)->toBe($provider)
        ->and($provider->build()->id)->toBe('openai:gpt-4');
});

test('id method can be chained with other methods', function () {
    $provider = (new Provider)
        ->id('openai:gpt-4')
        ->label('Custom Label')
        ->temperature(0.7);

    expect($provider->build()->id)->toBe('openai:gpt-4')
        ->and($provider->build()->label)->toBe('Custom Label')
        ->and($provider->build()->temperature)->toBe(0.7);
});

test('id method can be called multiple times to update the id', function () {
    $provider = new Provider;

    $provider->id('openai:gpt-4');
    expect($provider->build()->id)->toBe('openai:gpt-4');

    $provider->id('anthropic:claude-3');
    expect($provider->build()->id)->toBe('anthropic:claude-3');
});

test('extended method can be called and returns self for chaining', function () {
    (new Provider)->extend('customMethod', function (Provider $provider, string $value): void {
        $provider->label("custom-{$value}");
    });

    $provider = (new Provider)
        ->id('openai:gpt-4')
        ->customMethod('test');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and($provider->build()->label)->toBe('custom-test');
});

test('extended method receives arguments correctly', function () {
    (new Provider)->extend('withCustomConfig', function (Provider $provider, string $key, mixed $value): void {
        $provider->config([$key => $value]);
    });

    $provider = (new Provider)
        ->id('openai:gpt-4')
        ->withCustomConfig('apiKey', 'test-key');

    expect($provider->build()->config)->toBe(['apiKey' => 'test-key']);
});

test('multiple extensions can be registered and used', function () {
    (new Provider)->extend('setModel', function (Provider $provider, string $model): void {
        $provider->id($model);
    });

    (new Provider)->extend('setCreative', function (Provider $provider): void {
        $provider->temperature(0.9);
    });

    $provider = (new Provider)
        ->setModel('openai:gpt-4')
        ->setCreative();

    expect($provider->build()->id)->toBe('openai:gpt-4')
        ->and($provider->build()->temperature)->toBe(0.9);
});

test('calling unknown method throws BadMethodCallException', function () {
    $provider = new Provider;

    $provider->nonExistentMethod();
})->throws(\BadMethodCallException::class, 'Method nonExistentMethod does not exist on KevinPijning\Prompt\Provider');

test('extended methods can be chained with native methods', function () {
    (new Provider)->extend('preset', function (Provider $provider, string $name): void {
        match ($name) {
            'creative' => $provider->temperature(0.9)->topP(0.95),
            'precise' => $provider->temperature(0.1)->topP(0.1),
            default => null,
        };
    });

    $provider = (new Provider)
        ->id('openai:gpt-4')
        ->preset('creative')
        ->maxTokens(1000);

    expect($provider->build()->id)->toBe('openai:gpt-4')
        ->and($provider->build()->temperature)->toBe(0.9)
        ->and($provider->build()->topP)->toBe(0.95)
        ->and($provider->build()->maxTokens)->toBe(1000);
});
