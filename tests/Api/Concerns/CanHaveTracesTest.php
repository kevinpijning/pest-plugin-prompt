<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toHaveTraceSpanCount creates a trace-span-count assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $patterns = ['pattern1', 'pattern2'];

    $result = $testCase->toHaveTraceSpanCount($patterns, min: 1, max: 5);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('trace-span-count')
        ->and($assertion->value)->toBe($patterns)
        ->and($assertion->options)->toHaveKey('patterns')
        ->and($assertion->options['patterns'])->toBe($patterns)
        ->and($assertion->options)->toHaveKey('min')
        ->and($assertion->options['min'])->toBe(1)
        ->and($assertion->options)->toHaveKey('max')
        ->and($assertion->options['max'])->toBe(5);
});

test('toHaveTraceSpanCount can be called without min/max', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $patterns = ['pattern1'];

    $testCase->toHaveTraceSpanCount($patterns);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('patterns')
        ->and($assertion->options)->not->toHaveKey('min')
        ->and($assertion->options)->not->toHaveKey('max');
});

test('toHaveTraceSpanDuration creates a trace-span-duration assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $patterns = ['pattern1'];

    $result = $testCase->toHaveTraceSpanDuration($patterns, percentile: 0.95, maxDuration: 1000.0);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion->type)->toBe('trace-span-duration')
        ->and($assertion->options)->toHaveKey('patterns')
        ->and($assertion->options)->toHaveKey('percentile')
        ->and($assertion->options['percentile'])->toBe(0.95)
        ->and($assertion->options)->toHaveKey('maxDuration')
        ->and($assertion->options['maxDuration'])->toBe(1000.0);
});

test('toHaveTraceErrorSpans creates a trace-error-spans assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveTraceErrorSpans();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('trace-error-spans');
});

test('toHaveTraceErrorSpans accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $options = ['custom' => 'value'];

    $testCase->toHaveTraceErrorSpans($options);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('custom');
});
