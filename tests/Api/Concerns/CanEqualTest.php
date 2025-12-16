<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toEqual creates an assertion with default parameters', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $expectedValue = 'The expected output';

    // Act
    $result = $testCase->toEqual($expectedValue);

    // Assert
    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];

    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe($expectedValue)
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBeArray()->toBeEmpty();
});

test('toEqual can be chained', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    // Act
    $result = $testCase
        ->toEqual('first value')
        ->toEqual('second value');

    // Assert
    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(2);

    $assertions = $testCase->build()->assertions;

    expect($assertions[0]->value)->toBe('first value')
        ->and($assertions[1]->value)->toBe('second value')
        ->and($assertions[0]->type)->toBe('equals')
        ->and($assertions[1]->type)->toBe('equals');
});

test('toEqual accepts integer values', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    // Act
    $testCase->toEqual(42);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe(42);
});

test('toEqual accepts float values', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    // Act
    $testCase->toEqual(3.14);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe(3.14);
});

test('toEqual accepts boolean values', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    // Act
    $testCase->toEqual(true);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe(true);
});

test('toEqual accepts array values', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $expectedArray = ['key' => 'value', 'number' => 42];

    // Act
    $testCase->toEqual($expectedArray);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe($expectedArray);
});

test('toEqual accepts null values', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    // Act
    $testCase->toEqual(null);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBeNull();
});

test('toBe is an alias for toEqual', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    // Act
    $testCase->toBe('first value');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('equals')
        ->and($assertion->value)->toBe('first value');
});
