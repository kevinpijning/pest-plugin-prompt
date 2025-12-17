<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toHaveCost creates a cost assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveCost(0.01);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('cost')
        ->and($assertion->threshold)->toBe(0.01);
});

test('toHaveLatency creates a latency assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveLatency(1000);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('latency')
        ->and($assertion->threshold)->toBe(1000.0);
});

test('can chain performance methods', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase
        ->toHaveCost(0.01)
        ->toHaveLatency(1000);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(2);
});
