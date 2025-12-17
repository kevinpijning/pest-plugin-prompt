<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toHaveTraceSpanCount creates a trace-span-count assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $patterns = ['pattern1', 'pattern2'];

    $result = $testCase->toHaveTraceSpanCount($patterns, min: 1, max: 5);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('trace-span-count')
        ->and($assertion->value)->toHaveKey('patterns')
        ->and($assertion->value['patterns'])->toBe($patterns)
        ->and($assertion->value)->toHaveKey('min')
        ->and($assertion->value['min'])->toBe(1)
        ->and($assertion->value)->toHaveKey('max')
        ->and($assertion->value['max'])->toBe(5);
});

test('toHaveTraceSpanCount can be called without min/max', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $patterns = ['pattern1'];

    $testCase->toHaveTraceSpanCount($patterns);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toHaveKey('patterns')
        ->and($assertion->value)->not->toHaveKey('min')
        ->and($assertion->value)->not->toHaveKey('max');
});

test('toHaveTraceSpanDuration creates a trace-span-duration assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $patterns = ['pattern1'];

    $result = $testCase->toHaveTraceSpanDuration($patterns, percentile: 0.95, maxDuration: 1000.0);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('trace-span-duration')
        ->and($assertion->value)->toHaveKey('patterns')
        ->and($assertion->value)->toHaveKey('percentile')
        ->and($assertion->value['percentile'])->toBe(0.95)
        ->and($assertion->value)->toHaveKey('maxDuration')
        ->and($assertion->value['maxDuration'])->toBe(1000.0);
});

test('toHaveTraceErrorSpans creates a trace-error-spans assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveTraceErrorSpans();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('trace-error-spans');
});
