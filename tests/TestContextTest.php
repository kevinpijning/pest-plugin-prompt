<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\Provider;
use KevinPijning\Prompt\TestContext;

beforeEach(function () {
    TestContext::clear();
});

test('getCurrentEvaluations returns empty array initially', function () {
    $evaluations = TestContext::getCurrentEvaluations();

    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('addEvaluation adds an evaluation to the context and returns it', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);

    $result = TestContext::addEvaluation($evaluation);

    expect($result)->toBe($evaluation)
        ->and(TestContext::getCurrentEvaluations())->toHaveCount(1)
        ->and(TestContext::getCurrentEvaluations()[0])->toBe($evaluation)
        ->and(TestContext::getCurrentEvaluations()[0])->toBeInstanceOf(Evaluation::class);
});

test('addEvaluation can add multiple evaluations', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);
    $evaluation3 = new Evaluation(['prompt3']);

    TestContext::addEvaluation($evaluation1);
    TestContext::addEvaluation($evaluation2);
    TestContext::addEvaluation($evaluation3);

    $evaluations = TestContext::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(3)
        ->and($evaluations[0])->toBe($evaluation1)
        ->and($evaluations[1])->toBe($evaluation2)
        ->and($evaluations[2])->toBe($evaluation3);
});

test('clear removes all evaluations from the context', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);

    TestContext::addEvaluation($evaluation1);
    TestContext::addEvaluation($evaluation2);

    expect(TestContext::getCurrentEvaluations())->toHaveCount(2);

    TestContext::clear();

    $evaluations = TestContext::getCurrentEvaluations();
    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('clear works when context is already empty', function () {
    TestContext::clear();

    $evaluations = TestContext::getCurrentEvaluations();
    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('evaluations persist until clear is called', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);

    TestContext::addEvaluation($evaluation1);
    expect(TestContext::getCurrentEvaluations())->toHaveCount(1);

    TestContext::addEvaluation($evaluation2);
    expect(TestContext::getCurrentEvaluations())->toHaveCount(2);

    // Evaluations should still be there
    expect(TestContext::getCurrentEvaluations())->toHaveCount(2);

    TestContext::clear();
    expect(TestContext::getCurrentEvaluations())->toBeEmpty();
});

test('prompt function adds evaluation to TestContext', function () {
    $evaluation = prompt('test prompt');

    $evaluations = TestContext::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(1)
        ->and($evaluations[0])->toBe($evaluation)
        ->and($evaluations[0])->toBeInstanceOf(Evaluation::class);
});

test('prompt function can add multiple evaluations to TestContext', function () {
    $evaluation1 = prompt('first prompt');
    $evaluation2 = prompt('second prompt');
    $evaluation3 = prompt('third prompt');

    $evaluations = TestContext::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(3)
        ->and($evaluations[0])->toBe($evaluation1)
        ->and($evaluations[1])->toBe($evaluation2)
        ->and($evaluations[2])->toBe($evaluation3);
});

test('addProvider adds a provider to the context and returns it', function () {
    $provider = new Provider;
    $provider->id('openai:gpt-4');

    $result = TestContext::addProvider('my-provider', $provider);

    expect($result)->toBe($provider)
        ->and(TestContext::hasProvider('my-provider'))->toBeTrue()
        ->and(TestContext::getProvider('my-provider'))->toBe($provider);
});

test('addProvider can add multiple providers with different names', function () {
    $provider1 = Provider::create('openai:gpt-4');
    $provider2 = Provider::create('anthropic:claude-3');
    $provider3 = Provider::create('google:gemini');

    TestContext::addProvider('openai', $provider1);
    TestContext::addProvider('anthropic', $provider2);
    TestContext::addProvider('google', $provider3);

    expect(TestContext::hasProvider('openai'))->toBeTrue()
        ->and(TestContext::hasProvider('anthropic'))->toBeTrue()
        ->and(TestContext::hasProvider('google'))->toBeTrue()
        ->and(TestContext::getProvider('openai'))->toBe($provider1)
        ->and(TestContext::getProvider('anthropic'))->toBe($provider2)
        ->and(TestContext::getProvider('google'))->toBe($provider3);
});

test('hasProvider returns false for non-existent provider', function () {
    expect(TestContext::hasProvider('non-existent'))->toBeFalse();
});

test('hasProvider returns true for existing provider', function () {
    $provider = Provider::create('openai:gpt-4');
    TestContext::addProvider('my-provider', $provider);

    expect(TestContext::hasProvider('my-provider'))->toBeTrue();
});

test('getProvider returns the correct provider', function () {
    $provider = Provider::create('openai:gpt-4')
        ->label('My Custom Provider')
        ->temperature(0.7);

    TestContext::addProvider('custom', $provider);

    $retrieved = TestContext::getProvider('custom');

    expect($retrieved)->toBe($provider)
        ->and($retrieved->build()->id)->toBe('openai:gpt-4')
        ->and($retrieved->build()->label)->toBe('My Custom Provider')
        ->and($retrieved->build()->temperature)->toBe(0.7);
});

test('addProvider overwrites existing provider with same name', function () {
    $provider1 = Provider::create('openai:gpt-4');
    $provider2 = Provider::create('anthropic:claude-3');

    TestContext::addProvider('my-provider', $provider1);
    expect(TestContext::getProvider('my-provider'))->toBe($provider1);

    TestContext::addProvider('my-provider', $provider2);
    expect(TestContext::getProvider('my-provider'))->toBe($provider2)
        ->and(TestContext::getProvider('my-provider'))->not->toBe($provider1);
});

test('clear does not remove providers, only evaluations', function () {
    $provider = Provider::create('openai:gpt-4');
    $evaluation = new Evaluation(['test prompt']);

    TestContext::addProvider('my-provider', $provider);
    TestContext::addEvaluation($evaluation);

    expect(TestContext::hasProvider('my-provider'))->toBeTrue()
        ->and(TestContext::getCurrentEvaluations())->toHaveCount(1);

    TestContext::clear();

    expect(TestContext::hasProvider('my-provider'))->toBeTrue()
        ->and(TestContext::getProvider('my-provider'))->toBe($provider)
        ->and(TestContext::getCurrentEvaluations())->toBeEmpty();
});
