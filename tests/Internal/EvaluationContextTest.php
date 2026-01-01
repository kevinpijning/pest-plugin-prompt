<?php

declare(strict_types=1);

use KevinPijning\Prompt\AssertionGroup;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\AssertionGroupContext;
use KevinPijning\Prompt\Internal\EvaluationContext;
use KevinPijning\Prompt\Internal\ProviderContext;
use KevinPijning\Prompt\Provider;

beforeEach(function () {
    EvaluationContext::clear();
});

test('getCurrentEvaluations returns empty array initially', function () {
    $evaluations = EvaluationContext::getCurrentEvaluations();

    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('addEvaluation adds an evaluation to the context and returns it', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);

    $result = EvaluationContext::addEvaluation($evaluation);

    expect($result)->toBe($evaluation)
        ->and(EvaluationContext::getCurrentEvaluations())->toHaveCount(1)
        ->and(EvaluationContext::getCurrentEvaluations()[0])->toBe($evaluation)
        ->and(EvaluationContext::getCurrentEvaluations()[0])->toBeInstanceOf(Evaluation::class);
});

test('addEvaluation can add multiple evaluations', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);
    $evaluation3 = new Evaluation(['prompt3']);

    EvaluationContext::addEvaluation($evaluation1);
    EvaluationContext::addEvaluation($evaluation2);
    EvaluationContext::addEvaluation($evaluation3);

    $evaluations = EvaluationContext::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(3)
        ->and($evaluations[0])->toBe($evaluation1)
        ->and($evaluations[1])->toBe($evaluation2)
        ->and($evaluations[2])->toBe($evaluation3);
});

test('clear removes all evaluations from the context', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);

    EvaluationContext::addEvaluation($evaluation1);
    EvaluationContext::addEvaluation($evaluation2);

    expect(EvaluationContext::getCurrentEvaluations())->toHaveCount(2);

    EvaluationContext::clear();

    $evaluations = EvaluationContext::getCurrentEvaluations();
    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('clear works when context is already empty', function () {
    EvaluationContext::clear();

    $evaluations = EvaluationContext::getCurrentEvaluations();
    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('evaluations persist until clear is called', function () {
    $evaluation1 = new Evaluation(['prompt1']);
    $evaluation2 = new Evaluation(['prompt2']);

    EvaluationContext::addEvaluation($evaluation1);
    expect(EvaluationContext::getCurrentEvaluations())->toHaveCount(1);

    EvaluationContext::addEvaluation($evaluation2);
    expect(EvaluationContext::getCurrentEvaluations())->toHaveCount(2);

    // Evaluations should still be there
    expect(EvaluationContext::getCurrentEvaluations())->toHaveCount(2);

    EvaluationContext::clear();
    expect(EvaluationContext::getCurrentEvaluations())->toBeEmpty();
});

test('prompt function adds evaluation to TestContext', function () {
    $evaluation = prompt('test prompt');

    $evaluations = EvaluationContext::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(1)
        ->and($evaluations[0])->toBe($evaluation)
        ->and($evaluations[0])->toBeInstanceOf(Evaluation::class);
});

test('prompt function can add multiple evaluations to TestContext', function () {
    $evaluation1 = prompt('first prompt');
    $evaluation2 = prompt('second prompt');
    $evaluation3 = prompt('third prompt');

    $evaluations = EvaluationContext::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(3)
        ->and($evaluations[0])->toBe($evaluation1)
        ->and($evaluations[1])->toBe($evaluation2)
        ->and($evaluations[2])->toBe($evaluation3);
});

test('clear does not remove providers, only evaluations', function () {
    $provider = Provider::create('openai:gpt-4');
    $evaluation = new Evaluation(['test prompt']);

    ProviderContext::add('my-provider', $provider);
    EvaluationContext::addEvaluation($evaluation);

    expect(ProviderContext::has('my-provider'))->toBeTrue()
        ->and(EvaluationContext::getCurrentEvaluations())->toHaveCount(1);

    EvaluationContext::clear();

    expect(ProviderContext::has('my-provider'))->toBeTrue()
        ->and(ProviderContext::get('my-provider'))->toBe($provider)
        ->and(EvaluationContext::getCurrentEvaluations())->toBeEmpty();
});

test('clear does not remove assertion groups, only evaluations', function () {
    $group = new AssertionGroup('be nice');
    $evaluation = new Evaluation(['test prompt']);

    AssertionGroupContext::add('be nice', $group);
    EvaluationContext::addEvaluation($evaluation);

    expect(AssertionGroupContext::has('be nice'))->toBeTrue()
        ->and(EvaluationContext::getCurrentEvaluations())->toHaveCount(1);

    EvaluationContext::clear();

    expect(AssertionGroupContext::has('be nice'))->toBeTrue()
        ->and(AssertionGroupContext::get('be nice'))->toBe($group)
        ->and(EvaluationContext::getCurrentEvaluations())->toBeEmpty();
});
