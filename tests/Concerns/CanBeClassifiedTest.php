<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toBeClassified creates a classifier assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeClassified('huggingface:model', 'positive', threshold: 0.8);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('classifier')
        ->and($assertion->value)->toBe('positive')
        ->and($assertion->threshold)->toBe(0.8)
        ->and($assertion->provider)->toBe('huggingface:model');
});

test('toBeClassified can be called without threshold', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toBeClassified('provider', 'class');

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->threshold)->toBeNull()
        ->and($assertion->provider)->toBe('provider');
});
