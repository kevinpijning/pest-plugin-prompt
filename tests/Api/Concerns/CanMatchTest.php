<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('startsWith creates a starts-with assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->startsWith('Hello');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('starts-with')
        ->and($assertion->value)->toBe('Hello')
        ->and($assertion->threshold)->toBeNull();
});

test('startsWith accepts threshold parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->startsWith('Hello', threshold: 0.8);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->threshold)->toBe(0.8);
});

test('startsWith accepts options parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $options = ['custom' => 'value'];

    $testCase->startsWith('Hello', options: $options);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->options)->toBeArray()->toHaveKey('custom');
});

test('toMatchRegex creates a regex assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toMatchRegex('/\d+/');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('regex')
        ->and($assertion->value)->toBe('/\d+/')
        ->and($assertion->threshold)->toBeNull();
});

test('toMatchRegex accepts threshold parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toMatchRegex('/\d+/', threshold: 0.9);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->threshold)->toBe(0.9);
});

test('can chain match methods', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase
        ->startsWith('Hello')
        ->toMatchRegex('/\d+/');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(2);
});
