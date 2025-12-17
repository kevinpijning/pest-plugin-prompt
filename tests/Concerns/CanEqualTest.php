<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toEqual creates an assertion with default parameters', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $expectedValue = 'The expected output';

    $result = $testCase->toEqual($expectedValue);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];

    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe($expectedValue)
        ->and($assertion->threshold)->toBeNull();
});

test('toEqual can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toEqual('first value')
        ->toEqual('second value');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(2);

    $assertions = $testCase->build()->assertions;

    expect($assertions[0]->value)->toBe('first value')
        ->and($assertions[1]->value)->toBe('second value')
        ->and($assertions[0]->type)->toBe('equals')
        ->and($assertions[1]->type)->toBe('equals');
});

test('toEqual accepts integer values', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toEqual(42);

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe(42);
});

test('toEqual accepts float values', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toEqual(3.14);

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe(3.14);
});

test('toEqual accepts boolean values', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toEqual(true);

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe(true);
});

test('toEqual accepts array values', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $expectedArray = ['key' => 'value', 'number' => 42];

    $testCase->toEqual($expectedArray);

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe($expectedArray);
});

test('toEqual accepts null values', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toEqual(null);

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBeNull();
});

test('toBe is an alias for toEqual', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toBe('first value');

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe('first value');
});
