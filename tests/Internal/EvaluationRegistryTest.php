<?php

declare(strict_types=1);

use KevinPijning\Prompt\AssertionGroup;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\AssertionGroupRegistry;
use KevinPijning\Prompt\Internal\EvaluationRegistry;
use KevinPijning\Prompt\Internal\ProviderRegistry;
use KevinPijning\Prompt\Provider;

beforeEach(function () {
    EvaluationRegistry::clear();
});

test('getCurrentEvaluations returns empty array initially', function () {
    $evaluations = EvaluationRegistry::getCurrentEvaluations();

    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('addEvaluation adds an evaluation to the context and returns it', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);

    $result = EvaluationRegistry::addEvaluation($evaluation);

    expect($result)->toBe($evaluation)
        ->and(EvaluationRegistry::getCurrentEvaluations())->toHaveCount(1)
        ->and(EvaluationRegistry::getCurrentEvaluations()[0])->toBe($evaluation)
        ->and(EvaluationRegistry::getCurrentEvaluations()[0])->toBeInstanceOf(Evaluation::class);
});

test('addEvaluation can add multiple evaluations', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);
    $evaluation3 = new Evaluation(['prompt3']);

    EvaluationRegistry::addEvaluation($evaluation1);
    EvaluationRegistry::addEvaluation($evaluation2);
    EvaluationRegistry::addEvaluation($evaluation3);

    $evaluations = EvaluationRegistry::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(3)
        ->and($evaluations[0])->toBe($evaluation1)
        ->and($evaluations[1])->toBe($evaluation2)
        ->and($evaluations[2])->toBe($evaluation3);
});

test('clear removes all evaluations from the context', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);

    EvaluationRegistry::addEvaluation($evaluation1);
    EvaluationRegistry::addEvaluation($evaluation2);

    expect(EvaluationRegistry::getCurrentEvaluations())->toHaveCount(2);

    EvaluationRegistry::clear();

    $evaluations = EvaluationRegistry::getCurrentEvaluations();
    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('clear works when context is already empty', function () {
    EvaluationRegistry::clear();

    $evaluations = EvaluationRegistry::getCurrentEvaluations();
    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('evaluations persist until clear is called', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);

    EvaluationRegistry::addEvaluation($evaluation1);
    expect(EvaluationRegistry::getCurrentEvaluations())->toHaveCount(1);

    EvaluationRegistry::addEvaluation($evaluation2);
    expect(EvaluationRegistry::getCurrentEvaluations())->toHaveCount(2);

    // Evaluations should still be there
    expect(EvaluationRegistry::getCurrentEvaluations())->toHaveCount(2);

    EvaluationRegistry::clear();
    expect(EvaluationRegistry::getCurrentEvaluations())->toBeEmpty();
});

test('prompt function adds evaluation to TestContext', function () {
    $evaluation = prompt('test prompt');

    $evaluations = EvaluationRegistry::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(1)
        ->and($evaluations[0])->toBe($evaluation)
        ->and($evaluations[0])->toBeInstanceOf(Evaluation::class);
});

test('prompt function can add multiple evaluations to TestContext', function () {
    $evaluation1 = prompt('first prompt');
    $evaluation2 = prompt('second prompt');
    $evaluation3 = prompt('third prompt');

    $evaluations = EvaluationRegistry::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(3)
        ->and($evaluations[0])->toBe($evaluation1)
        ->and($evaluations[1])->toBe($evaluation2)
        ->and($evaluations[2])->toBe($evaluation3);
});

test('clear does not remove providers, only evaluations', function () {
    $provider = Provider::create('openai:gpt-4');
    $evaluation = new Evaluation(['test prompt']);

    ProviderRegistry::add('my-provider', $provider);
    EvaluationRegistry::addEvaluation($evaluation);

    expect(ProviderRegistry::has('my-provider'))->toBeTrue()
        ->and(EvaluationRegistry::getCurrentEvaluations())->toHaveCount(1);

    EvaluationRegistry::clear();

    expect(ProviderRegistry::has('my-provider'))->toBeTrue()
        ->and(ProviderRegistry::get('my-provider'))->toBe($provider)
        ->and(EvaluationRegistry::getCurrentEvaluations())->toBeEmpty();
});

test('clear does not remove assertion groups, only evaluations', function () {
    $group = new AssertionGroup('be nice');
    $evaluation = new Evaluation(['test prompt']);

    AssertionGroupRegistry::add('be nice', $group);
    EvaluationRegistry::addEvaluation($evaluation);

    expect(AssertionGroupRegistry::has('be nice'))->toBeTrue()
        ->and(EvaluationRegistry::getCurrentEvaluations())->toHaveCount(1);

    EvaluationRegistry::clear();

    expect(AssertionGroupRegistry::has('be nice'))->toBeTrue()
        ->and(AssertionGroupRegistry::get('be nice'))->toBe($group)
        ->and(EvaluationRegistry::getCurrentEvaluations())->toBeEmpty();
});
