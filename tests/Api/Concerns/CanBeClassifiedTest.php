<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toBeClassified creates a classifier assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeClassified('huggingface:model', 'positive', threshold: 0.8);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('classifier')
        ->and($assertion->value)->toBe('positive')
        ->and($assertion->threshold)->toBe(0.8)
        ->and($assertion->options)->toHaveKey('provider')
        ->and($assertion->options['provider'])->toBe('huggingface:model');
});

test('toBeClassified accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $options = ['custom' => 'value'];

    $testCase->toBeClassified('provider', 'class', options: $options);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('custom')
        ->and($assertion->options)->toHaveKey('provider');
});

