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

    expect($provider)->toBeInstanceOf(Provider::class)
        ->getLabel()->toBe('Custom label')
        ->getMaxTokens()->toBe(1234)
        ->getTopP()->toBe(.1)
        ->getFrequencyPenalty()->toBe(.2)
        ->getPresencePenalty()->toBe(.3)
        ->getStop()->toBe(['\n', 'Human:', 'AI:']);
});

test('the provider accepts a provider id', function () {
    $provider = Provider::create('openai:gpt-4o-mini');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and($provider->getId())->toBe('openai:gpt-4o-mini');
});

test('a label can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->label('custom label');

    expect($provider->getLabel()
    )->toBe('custom label');
});

test('the temperature can be ser', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->temperature(.3);

    expect($provider->getTemperature())->toBe(.3);
});

test('the max token can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->maxTokens(3);
    expect($provider->getMaxTokens())->toBe(3);
});

test('the top p can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->topP(.8);
    expect($provider->getTopP())->toBe(.8);
});

test('the frequency penalty can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->frequencyPenalty(.1);

    expect($provider->getFrequencyPenalty())->toBe(.1);
});

test('the presence penalty can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->presencePenalty(.2);

    expect($provider->getPresencePenalty())->toBe(.2);
});

test('custom config can be set', function () {
    $provider = Provider::create('openai:gpt-4o-mini')
        ->config([
            'apiKey' => 'fake-api-key',
        ]);

    expect($provider->getConfig())->toBe(['apiKey' => 'fake-api-key']);
});
