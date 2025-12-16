<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Enums\FinishReason;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toHaveFinishReason creates a finish-reason assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveFinishReason('stop');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('finish-reason')
        ->and($assertion->value)->toBe('stop');
});

test('toHaveFinishReason accepts FinishReason enum', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toHaveFinishReason(FinishReason::Stop);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe('stop');
});

test('toHaveFinishReason accepts different reason values', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toHaveFinishReason('length');

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe('length');
});

test('toHaveFinishReason accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $options = ['custom' => 'value'];

    $testCase->toHaveFinishReason('stop', $options);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->options)->toHaveKey('custom');
});

test('toHaveFinishReasonStop creates assertion with stop reason', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveFinishReasonStop();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('finish-reason')
        ->and($assertion->value)->toBe('stop');
});

test('toHaveFinishReasonLength creates assertion with length reason', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveFinishReasonLength();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('finish-reason')
        ->and($assertion->value)->toBe('length');
});

test('toHaveFinishReasonContentFilter creates assertion with content_filter reason', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveFinishReasonContentFilter();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('finish-reason')
        ->and($assertion->value)->toBe('content_filter');
});

test('toHaveFinishReasonToolCalls creates assertion with tool_calls reason', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveFinishReasonToolCalls();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('finish-reason')
        ->and($assertion->value)->toBe('tool_calls');
});

test('convenience methods accept options parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $options = ['custom' => 'value'];

    $testCase->toHaveFinishReasonStop($options);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->options)->toHaveKey('custom');
});
