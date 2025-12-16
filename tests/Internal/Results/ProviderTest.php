<?php

declare(strict_types=1);

use KevinPijning\Prompt\Internal\Results\Provider;

test('Provider can be instantiated with id and label', function () {
    $provider = new Provider(
        id: 'openai:gpt-4o-mini',
        label: 'OpenAI GPT-4o Mini',
    );

    expect($provider->id)->toBe('openai:gpt-4o-mini')
        ->and($provider->label)->toBe('OpenAI GPT-4o Mini');
});

test('Provider can have empty label', function () {
    $provider = new Provider(
        id: 'openai:gpt-4o-mini',
        label: '',
    );

    expect($provider->id)->toBe('openai:gpt-4o-mini')
        ->and($provider->label)->toBe('');
});
