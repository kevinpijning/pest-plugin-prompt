<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\Provider;
use KevinPijning\Prompt\Promptable;
use KevinPijning\Prompt\TestContext;

beforeEach(function () {
    TestContext::clear();
});

test('Promptable trait provides prompt method that delegates to global prompt function', function () {
    $testObject = new class
    {
        use Promptable;
    };

    $evaluation = $testObject->prompt('test prompt');

    expect($evaluation)->toBeInstanceOf(Evaluation::class)
        ->and(TestContext::getCurrentEvaluations())->toHaveCount(1)
        ->and(TestContext::getCurrentEvaluations()[0])->toBe($evaluation);
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
        ->and(TestContext::hasProvider('my-provider'))->toBeTrue()
        ->and(TestContext::getProvider('my-provider'))->toBe($provider)
        ->and($provider->getId())->toBe('openai:gpt-4')
        ->and($provider->getLabel())->toBe('Test Provider')
        ->and($provider->getTemperature())->toBe(0.7);
});

test('Promptable trait provider method works without config', function () {
    $testObject = new class
    {
        use Promptable;
    };

    $provider = $testObject->provider('simple-provider');

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and(TestContext::hasProvider('simple-provider'))->toBeTrue()
        ->and(TestContext::getProvider('simple-provider'))->toBe($provider);
});

test('Promptable trait methods can be chained in test context', function () {
    $testObject = new class
    {
        use Promptable;
    };

    $provider = $testObject->provider('chained-provider', fn (Provider $p) => $p->id('openai:gpt-4'));
    $evaluation = $testObject->prompt('test')
        ->usingProvider('chained-provider');

    expect($evaluation)->toBeInstanceOf(Evaluation::class)
        ->and($evaluation->providers())->toHaveCount(1)
        ->and($evaluation->providers()[0])->toBe($provider);
});
