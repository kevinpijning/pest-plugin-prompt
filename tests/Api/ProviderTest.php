<?php

use Pest\Prompt\Api\Provider;

test('provider', function () {
    $provider = Provider::id('openai:gpt-4o-mini');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and($provider->id)->toBe('openai:gpt-4o-mini');
});
