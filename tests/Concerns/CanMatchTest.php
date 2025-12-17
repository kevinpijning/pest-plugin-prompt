<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

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

test('can chain match methods', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase
        ->startsWith('Hello')
        ->toMatchRegex('/\d+/');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(2);
});
