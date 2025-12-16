<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('it can be instantiated with variables and evaluation', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    expect($testCase)->toBeInstanceOf(TestCase::class);
});

test('it returns the variables', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    expect($testCase->variables())->toBe($variables);
});

test('it returns an empty array of assertions initially', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    expect($testCase->assertions())->toBe([]);
});

test('it can add an assertion', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $assertion = new Assertion('contains', 'test value');

    $result = $testCase->assert($assertion);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1)
        ->and($testCase->assertions()[0])->toBe($assertion);
});

test('it can add multiple assertions', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $assertion1 = new Assertion('contains', 'value1');
    $assertion2 = new Assertion('icontain', 'value2');

    $testCase->assert($assertion1);
    $testCase->assert($assertion2);

    expect($testCase->assertions())->toHaveCount(2)
        ->and($testCase->assertions()[0])->toBe($assertion1)
        ->and($testCase->assertions()[1])->toBe($assertion2);
});

test('and method returns a new TestCase from evaluation', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $newVariables = ['key3' => 'value3'];

    $result = $testCase->and($newVariables);

    expect($result)->toBeInstanceOf(TestCase::class)
        ->and($result)->not->toBe($testCase)
        ->and($result->variables())->toBe($newVariables);
});

test('and method can be chained with assertions', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $newVariables = ['key3' => 'value3'];

    $result = $testCase
        ->toContain('first')
        ->and($newVariables)
        ->toContain('second');

    expect($result)->toBeInstanceOf(TestCase::class)
        ->and($result)->not->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1)
        ->and($result->assertions())->toHaveCount(1);
});

test('expect method returns a new TestCase from evaluation', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $newVariables = ['key3' => 'value3'];

    $result = $testCase->expect($newVariables);

    expect($result)->toBeInstanceOf(TestCase::class)
        ->and($result)->not->toBe($testCase)
        ->and($result->variables())->toBe($newVariables);
});

test('expect method can be chained with assertions', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $newVariables = ['key3' => 'value3'];

    $result = $testCase
        ->toContain('first')
        ->expect($newVariables)
        ->toContain('second');

    expect($result)->toBeInstanceOf(TestCase::class)
        ->and($result)->not->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1)
        ->and($result->assertions())->toHaveCount(1);
});

test('expect method can be called with empty variables array', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->expect([]);

    expect($result)->toBeInstanceOf(TestCase::class)
        ->and($result)->not->toBe($testCase)
        ->and($result->variables())->toBe([]);
});

test('expect and and methods are functionally equivalent', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);
    $newVariables = ['key2' => 'value2'];

    $result1 = $testCase->expect($newVariables);
    $result2 = $testCase->and($newVariables);

    expect($result1)->toBeInstanceOf(TestCase::class)
        ->and($result2)->toBeInstanceOf(TestCase::class)
        ->and($result1->variables())->toBe($newVariables)
        ->and($result2->variables())->toBe($newVariables)
        ->and($result1)->not->toBe($result2) // They create different instances
        ->and($evaluation->testCases())->toHaveCount(2); // Both are added to evaluation
});
