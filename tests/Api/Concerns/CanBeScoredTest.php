<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toBeScoredByPi creates a pi assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeScoredByPi('Is the response helpful?', threshold: 0.8);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('pi')
        ->and($assertion->value)->toBe('Is the response helpful?')
        ->and($assertion->threshold)->toBe(0.8);
});

test('toBeScoredByPi can be called without threshold', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toBeScoredByPi('Is the response helpful?');

    $assertion = $testCase->assertions()[0];
    expect($assertion->threshold)->toBeNull();
});

test('toBeScoredByPi accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $options = ['custom' => 'value'];

    $testCase->toBeScoredByPi('rubric', options: $options);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('custom');
});

