<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\Provider;
use KevinPijning\Prompt\Api\TestCase;
use KevinPijning\Prompt\TestContext;

test('it can be instantiated with prompts', function () {
    $prompts = ['prompt1', 'prompt2', 'prompt3'];
    $evaluation = new Evaluation($prompts);

    expect($evaluation)->toBeInstanceOf(Evaluation::class);
});

test('it can be instantiated with empty prompts array', function () {
    $evaluation = new Evaluation([]);

    expect($evaluation)->toBeInstanceOf(Evaluation::class);
});

test('describe method sets description and returns self', function () {
    $evaluation = new Evaluation(['prompt1']);
    $description = 'Test description';

    $result = $evaluation->describe($description);

    expect($result)->toBe($evaluation);
});

test('describe method can be chained', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->describe('First description')
        ->describe('Second description');

    expect($result)->toBe($evaluation);
});

test('usingProvider method adds a single provider and returns self', function () {
    $evaluation = new Evaluation(['prompt1']);
    $provider = 'openai:gpt-4';

    $result = $evaluation->usingProvider($provider);

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(1)
        ->and($result->providers()[0])->toBeInstanceOf(Provider::class)
        ->getId()->toBe('openai:gpt-4');
});

test('usingProvider method can add multiple providers', function () {
    $evaluation = new Evaluation(['prompt1']);
    $provider1 = 'openai:gpt-4';
    $provider2 = 'anthropic:claude-3';
    $provider3 = 'google:gemini';

    $result = $evaluation->usingProvider($provider1, $provider2, $provider3);

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(3)
        ->and($result->providers()[0])->toBeInstanceOf(Provider::class)
        ->getId()->toBe('openai:gpt-4');
});

test('usingProvider method can be called multiple times', function () {
    $evaluation = new Evaluation(['prompt1']);

    $evaluation->usingProvider('openai:gpt-4');
    $result = $evaluation->usingProvider('anthropic:claude-3');

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(2)
        ->and($result->providers()[0])->toBeInstanceOf(Provider::class)
        ->getId()->toBe('openai:gpt-4');
});

test('usingProvider method can be chained', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->usingProvider('openai:gpt-4')
        ->usingProvider('anthropic:claude-3')
        ->usingProvider('google:gemini');

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(3);
});

test('usingProvider method can accept a Provider class', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider(Provider::create('openai:gpt-4o-mini'));

    expect($result->providers())->toHaveCount(1)
        ->and($result->providers()[0])->toBeInstanceOf(Provider::class)
        ->getId()->toBe('openai:gpt-4o-mini');
});

test('expect method creates and returns a TestCase', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];

    $testCase = $evaluation->expect($variables);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->variables())->toBe($variables);
});

test('expect method can be called with no parameters', function () {
    $evaluation = new Evaluation(['prompt1']);

    $testCase = $evaluation->expect();

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->variables())->toBe([]);
});

test('expect method can be called with empty variables array', function () {
    $evaluation = new Evaluation(['prompt1']);

    $testCase = $evaluation->expect([]);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->variables())->toBe([]);
});

test('expect method can create multiple test cases', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables1 = ['key1' => 'value1'];
    $variables2 = ['key2' => 'value2'];
    $variables3 = ['key3' => 'value3'];

    $testCase1 = $evaluation->expect($variables1);
    $testCase2 = $evaluation->expect($variables2);
    $testCase3 = $evaluation->expect($variables3);

    expect($testCase1)->toBeInstanceOf(TestCase::class)
        ->and($testCase2)->toBeInstanceOf(TestCase::class)
        ->and($testCase3)->toBeInstanceOf(TestCase::class)
        ->and($testCase1)->not->toBe($testCase2)
        ->and($testCase2)->not->toBe($testCase3)
        ->and($testCase1->variables())->toBe($variables1)
        ->and($testCase2->variables())->toBe($variables2)
        ->and($testCase3->variables())->toBe($variables3);
});

test('clearTests method returns self', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->clearTests();

    expect($result)->toBe($evaluation);
});

test('clearTests method can be chained', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->clearTests()
        ->clearTests();

    expect($result)->toBe($evaluation);
});

test('methods can be chained together', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->describe('Test description')
        ->usingProvider('openai:gpt-4')
        ->expect(['key' => 'value'])
        ->toContain('test');

    expect($result)->toBeInstanceOf(TestCase::class);
});

test('expect method works after clearTests', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables1 = ['key1' => 'value1'];
    $variables2 = ['key2' => 'value2'];

    $testCase1 = $evaluation->expect($variables1);
    $evaluation->clearTests();
    $testCase2 = $evaluation->expect($variables2);

    expect($testCase1)->toBeInstanceOf(TestCase::class)
        ->and($testCase2)->toBeInstanceOf(TestCase::class)
        ->and($testCase2)->not->toBe($testCase1)
        ->and($testCase2->variables())->toBe($variables2);
});

test('usingProvider method can accept a callable that configures a provider', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider(function (Provider $provider) {
        return $provider->id('openai:gpt-4')
            ->label('Custom Label')
            ->temperature(0.7);
    });

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(1)
        ->and($result->providers()[0])->toBeInstanceOf(Provider::class)
        ->and($result->providers()[0]->getId())->toBe('openai:gpt-4')
        ->and($result->providers()[0]->getLabel())->toBe('Custom Label')
        ->and($result->providers()[0]->getTemperature())->toBe(0.7);
});

test('usingProvider method can accept multiple callables', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider(
        fn (Provider $p) => $p->id('openai:gpt-4'),
        fn (Provider $p) => $p->id('anthropic:claude-3')
    );

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(2)
        ->and($result->providers()[0]->getId())->toBe('openai:gpt-4')
        ->and($result->providers()[1]->getId())->toBe('anthropic:claude-3');
});

test('usingProvider method can use global provider from TestContext', function () {
    TestContext::clear();
    $evaluation = new Evaluation(['prompt1']);

    $globalProvider = Provider::create('openai:gpt-4')
        ->label('Global Provider')
        ->temperature(0.8);
    TestContext::addProvider('my-global-provider', $globalProvider);

    $result = $evaluation->usingProvider('my-global-provider');

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(1)
        ->and($result->providers()[0])->toBe($globalProvider)
        ->and($result->providers()[0]->getId())->toBe('openai:gpt-4')
        ->and($result->providers()[0]->getLabel())->toBe('Global Provider')
        ->and($result->providers()[0]->getTemperature())->toBe(0.8);
});

test('usingProvider method can mix global providers, callables, and direct providers', function () {
    TestContext::clear();
    $evaluation = new Evaluation(['prompt1']);

    $globalProvider = Provider::create('openai:gpt-4');
    TestContext::addProvider('global', $globalProvider);

    $directProvider = Provider::create('anthropic:claude-3');

    $result = $evaluation->usingProvider(
        'global',
        fn (Provider $p) => $p->id('google:gemini'),
        $directProvider
    );

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(3)
        ->and($result->providers()[0])->toBe($globalProvider)
        ->and($result->providers()[1]->getId())->toBe('google:gemini')
        ->and($result->providers()[2])->toBe($directProvider);
});

test('usingProvider method treats string as provider ID when not found in TestContext', function () {
    TestContext::clear();
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider('openai:gpt-4o-mini');

    expect($result)->toBe($evaluation)
        ->and($result->providers())->toHaveCount(1)
        ->and($result->providers()[0])->toBeInstanceOf(Provider::class)
        ->and($result->providers()[0]->getId())->toBe('openai:gpt-4o-mini');
});
