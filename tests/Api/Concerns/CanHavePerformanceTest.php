<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toHaveCost creates a cost assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveCost(0.01);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('cost')
        ->and($assertion->value)->toBe(0.01)
        ->and($assertion->threshold)->toBe(0.01);
});

test('toHaveCost can be called without maxCost', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toHaveCost();

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('cost')
        ->and($assertion->value)->toBeNull()
        ->and($assertion->threshold)->toBeNull();
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
        ->and($assertion->value)->toBe(1000)
        ->and($assertion->threshold)->toBe(1000.0);
});

test('toHaveLatency can be called without maxMilliseconds', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toHaveLatency();

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('latency')
        ->and($assertion->value)->toBeNull()
        ->and($assertion->threshold)->toBeNull();
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
