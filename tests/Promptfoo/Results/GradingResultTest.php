<?php

declare(strict_types=1);

use Pest\Prompt\Api\Assertion;
use Pest\Prompt\Promptfoo\Results\ComponentResult;
use Pest\Prompt\Promptfoo\Results\GradingResult;

test('GradingResult can be instantiated with all properties', function () {
    $componentResult = new ComponentResult(
        pass: true,
        score: 1.0,
        reason: 'Passed',
        assertion: new Assertion('contains', 'test'),
    );

    $gradingResult = new GradingResult(
        pass: true,
        score: 1.0,
        reason: 'All assertions passed',
        namedScores: [],
        tokensUsed: ['total' => 100],
        componentResults: [$componentResult],
    );

    expect($gradingResult->pass)->toBeTrue()
        ->and($gradingResult->score)->toBe(1.0)
        ->and($gradingResult->reason)->toBe('All assertions passed')
        ->and($gradingResult->namedScores)->toBeArray()
        ->and($gradingResult->tokensUsed)->toBeArray()
        ->and($gradingResult->tokensUsed['total'])->toBe(100)
        ->and($gradingResult->componentResults)->toHaveCount(1)
        ->and($gradingResult->componentResults[0])->toBe($componentResult);
});

test('GradingResult can have multiple component results', function () {
    $componentResult1 = new ComponentResult(
        pass: true,
        score: 1.0,
        reason: 'Passed',
        assertion: new Assertion('contains', 'test1'),
    );

    $componentResult2 = new ComponentResult(
        pass: false,
        score: 0.0,
        reason: 'Failed',
        assertion: new Assertion('contains', 'test2'),
    );

    $gradingResult = new GradingResult(
        pass: false,
        score: 0.5,
        reason: 'Some assertions failed',
        namedScores: [],
        tokensUsed: [],
        componentResults: [$componentResult1, $componentResult2],
    );

    expect($gradingResult->componentResults)->toHaveCount(2)
        ->and($gradingResult->componentResults[0]->pass)->toBeTrue()
        ->and($gradingResult->componentResults[1]->pass)->toBeFalse();
});

test('GradingResult can have empty component results', function () {
    $gradingResult = new GradingResult(
        pass: true,
        score: 1.0,
        reason: 'No assertions',
        namedScores: [],
        tokensUsed: [],
        componentResults: [],
    );

    expect($gradingResult->componentResults)->toBeArray()
        ->and($gradingResult->componentResults)->toBeEmpty();
});
