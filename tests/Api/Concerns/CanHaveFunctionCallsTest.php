<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toHaveValidFunctionCall creates an is-valid-function-call assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveValidFunctionCall();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('is-valid-function-call');
});

test('toHaveValidFunctionCall accepts schema parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $schema = ['type' => 'object'];

    $testCase->toHaveValidFunctionCall($schema);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('schema')
        ->and($assertion->options['schema'])->toBe($schema);
});

test('toHaveValidOpenaiFunctionCall creates an is-valid-openai-function-call assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveValidOpenaiFunctionCall();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion->type)->toBe('is-valid-openai-function-call');
});

test('toHaveValidOpenaiToolsCall creates an is-valid-openai-tools-call assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveValidOpenaiToolsCall();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion->type)->toBe('is-valid-openai-tools-call');
});

test('toHaveToolCallF1 creates a tool-call-f1 assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $expected = ['function1', 'function2'];

    $result = $testCase->toHaveToolCallF1($expected, threshold: 0.8);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion->type)->toBe('tool-call-f1')
        ->and($assertion->value)->toBe($expected)
        ->and($assertion->threshold)->toBe(0.8);
});
