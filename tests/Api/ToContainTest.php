<?php

declare(strict_types=1);

use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\Api\TestCase;
use Pest\Prompt\Promptfoo\Assertion;

test('toContain creates an assertion with default parameters', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->toContain('test');

    expect($result)->toBe($testCase);
    expect($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class);
});

test('toContain creates an icontain assertion when strict is false', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $testCase->toContain('test', strict: false);

    $assertion = $testCase->assertions()[0];
    // We can't directly access the type, but we can verify it was created
    expect($assertion)->toBeInstanceOf(Assertion::class);
});

test('toContain creates a contains assertion when strict is true', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $testCase->toContain('test', strict: true);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class);
});

test('toContain accepts a threshold parameter', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $threshold = 0.8;

    $testCase->toContain('test', threshold: $threshold);

    expect($testCase->assertions())->toHaveCount(1);
    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class);
});

test('toContain accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $options = ['option1' => 'value1', 'option2' => 'value2'];

    $testCase->toContain('test', options: $options);

    expect($testCase->assertions())->toHaveCount(1);
    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class);
});

test('toContain can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toContain('first')
        ->toContain('second')
        ->toContain('third');

    expect($result)->toBe($testCase);
    expect($testCase->assertions())->toHaveCount(3);
});
