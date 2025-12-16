<?php

use KevinPijning\Prompt\Api\Provider;

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

test('custom config can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->config([
            'apiKey' => 'fake-api-key',
        ]);

    expect($provider->build()->config)->toBe(['apiKey' => 'fake-api-key']);
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
