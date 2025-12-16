<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toBeJudged creates an assertion with default parameters', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = 'The response should be helpful and accurate';

    $result = $testCase->toBeJudged($contains);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('llm-rubric')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBe([]);
});

test('toBeJudged accepts a threshold parameter', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = 'The response should be helpful and accurate';
    $threshold = 0.8;

    $testCase->toBeJudged($contains, threshold: $threshold);

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('llm-rubric')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBe(0.8)
        ->and($assertion->options)->toBe([]);
});

test('toBeJudged accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = 'The response should be helpful and accurate';
    $options = ['option1' => 'value1', 'option2' => 'value2'];

    $testCase->toBeJudged($contains, options: $options);

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('llm-rubric')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBe($options);
});

test('toBeJudged accepts both threshold and options parameters', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = 'The response should be helpful and accurate';
    $threshold = 0.9;
    $options = ['option1' => 'value1', 'option2' => 'value2'];

    $testCase->toBeJudged($contains, threshold: $threshold, options: $options);

    expect($testCase->build()->assertions)->toHaveCount(1);
    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('llm-rubric')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBe(0.9)
        ->and($assertion->options)->toBe($options);
});

test('toBeJudged can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toBeJudged('First rubric criteria')
        ->toBeJudged('Second rubric criteria');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(2);

    $assertions = $testCase->build()->assertions;
    expect($assertions[0]->value)->toBe('First rubric criteria')
        ->and($assertions[1]->value)->toBe('Second rubric criteria')
        ->and($assertions[0]->type)->toBe('llm-rubric')
        ->and($assertions[1]->type)->toBe('llm-rubric');
});

test('toBeJudged can be chained with other assertion methods', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toBeJudged('The response should be helpful')
        ->toContain('expected text')
        ->toBeJudged('The response should be accurate', threshold: 0.8);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(3);

    $assertions = $testCase->build()->assertions;
    expect($assertions[0]->type)->toBe('llm-rubric')
        ->and($assertions[0]->value)->toBe('The response should be helpful')
        ->and($assertions[1]->type)->toBe('icontains')
        ->and($assertions[1]->value)->toBe('expected text')
        ->and($assertions[2]->type)->toBe('llm-rubric')
        ->and($assertions[2]->value)->toBe('The response should be accurate')
        ->and($assertions[2]->threshold)->toBe(0.8);
});
