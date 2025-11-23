<?php

declare(strict_types=1);

use Pest\Prompt\Api\Assertion;
use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\Api\TestCase;

test('toContain creates an assertion with default parameters', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->toContain('test');

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains')
        ->and($assertion->value)->toBe('test')
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBe([]);
});

test('toContain creates an icontain assertion when strict is false', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $testCase->toContain('test', strict: false);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains')
        ->and($assertion->value)->toBe('test')
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBe([]);
});

test('toContain creates a contains assertion when strict is true', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $testCase->toContain('test', strict: true);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('contains')
        ->and($assertion->value)->toBe('test')
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBe([]);
});

test('toContain accepts a threshold parameter', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $threshold = 0.8;

    $testCase->toContain('test', threshold: $threshold);

    expect($testCase->assertions())->toHaveCount(1);
    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains')
        ->and($assertion->value)->toBe('test')
        ->and($assertion->threshold)->toBe(0.8)
        ->and($assertion->options)->toBe([]);
});

test('toContain accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $options = ['option1' => 'value1', 'option2' => 'value2'];

    $testCase->toContain('test', options: $options);

    expect($testCase->assertions())->toHaveCount(1);
    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains')
        ->and($assertion->value)->toBe('test')
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBe($options);
});

test('toContain can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toContain('first')
        ->toContain('second')
        ->toContain('third');

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(3);

    $assertions = $testCase->assertions();
    expect($assertions[0]->value)->toBe('first')
        ->and($assertions[1]->value)->toBe('second')
        ->and($assertions[2]->value)->toBe('third')
        ->and($assertions[0]->type)->toBe('icontains')
        ->and($assertions[1]->type)->toBe('icontains')
        ->and($assertions[2]->type)->toBe('icontains');
});
