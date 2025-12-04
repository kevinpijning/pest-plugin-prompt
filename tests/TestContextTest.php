<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\TestContext;

use function KevinPijning\Prompt\prompt;

beforeEach(function () {
    TestContext::clear();
});

test('getCurrentEvaluations returns empty array initially', function () {
    $evaluations = TestContext::getCurrentEvaluations();

    expect($evaluations)->toBeArray()
        ->and($evaluations)->toBeEmpty();
});

test('addEvaluation adds an evaluation to the context', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);

    TestContext::addEvaluation($evaluation);

    $evaluations = TestContext::getCurrentEvaluations();
    expect($evaluations)->toHaveCount(1)
        ->and($evaluations[0])->toBe($evaluation)
        ->and($evaluations[0])->toBeInstanceOf(Evaluation::class);
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
