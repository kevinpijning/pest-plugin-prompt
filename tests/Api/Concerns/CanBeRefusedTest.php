<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toBeRefused creates an is-refusal assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeRefused();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('is-refusal')
        ->and($assertion->value)->toBeNull();
});

test('toBeRefused accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $options = ['custom' => 'value'];

    $testCase->toBeRefused($options);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('custom');
});
