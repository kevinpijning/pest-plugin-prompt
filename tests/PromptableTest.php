<?php

declare(strict_types=1);

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\EvaluationRegistry;
use KevinPijning\Prompt\Internal\ProviderContext;
use KevinPijning\Prompt\Promptable;
use KevinPijning\Prompt\Provider;

beforeEach(function () {
    EvaluationRegistry::clear();
});

test('Promptable trait provides prompt method that delegates to global prompt function', function () {
    $testObject = new class
    {
        use Promptable;
    };

    $evaluation = $testObject->prompt('test prompt');

    expect($evaluation)->toBeInstanceOf(Evaluation::class)
        ->and(EvaluationRegistry::getCurrentEvaluations())->toHaveCount(1)
        ->and(EvaluationRegistry::getCurrentEvaluations()[0])->toBe($evaluation);
});

test('Promptable trait provides provider method that delegates to global provider function', function () {
    $testObject = new class
    {
        use Promptable;
    };

    $provider = $testObject->provider('my-provider', function (Provider $p) {
        return $p->id('openai:gpt-4')
            ->label('Test Provider')
            ->temperature(0.7);
    });

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and(ProviderContext::has('my-provider'))->toBeTrue()
        ->and(ProviderContext::get('my-provider'))->toBe($provider)
        ->and($provider->build()->id)->toBe('openai:gpt-4')
        ->and($provider->build()->label)->toBe('Test Provider')
        ->and($provider->build()->temperature)->toBe(0.7);
});

test('Promptable trait provider method works without config', function () {
    $testObject = new class
    {
        use Promptable;
    };

    $provider = $testObject->provider('simple-provider');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and(ProviderContext::has('simple-provider'))->toBeTrue()
        ->and(ProviderContext::get('simple-provider'))->toBe($provider);
});

test('Promptable trait methods can be chained in test context', function () {
    $testObject = new class
    {
        use Promptable;
    };

    $provider = $testObject->provider('chained-provider', fn (Provider $p) => $p->id('openai:gpt-4'));
    $evaluation = $testObject->prompt('test')
        ->usingProvider('chained-provider');

    $built = $evaluation->build();
    expect($evaluation)->toBeInstanceOf(Evaluation::class)
        ->and($built->providers)->toHaveCount(1)
        ->and($built->providers[0]->id)->toBe('openai:gpt-4');
});
