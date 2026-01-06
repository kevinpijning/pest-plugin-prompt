<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toPassJavascript creates a javascript assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $code = 'return output.length > 10;';

    $result = $testCase->toPassJavascript($code);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(1);

    $assertion = $testCase->build()->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('javascript')
        ->and($assertion->value)->toBe($code);
});

test('toPassJavascript accepts threshold parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $code = 'return output.length / 100;';

    $testCase->toPassJavascript($code, threshold: 0.5);

    $assertion = $testCase->build()->assertions()[0];
    expect($assertion->threshold)->toBe(0.5);
});

test('toPassJavascript accepts config parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $code = 'return output.length > config.minLength;';
    $config = ['minLength' => 10];

    $testCase->toPassJavascript($code, config: $config);

    $assertion = $testCase->build()->assertions()[0];
    // User config is preserved alongside internal assertion_id
    expect($assertion->config)->toHaveKey('minLength')
        ->and($assertion->config['minLength'])->toBe(10)
        ->and($assertion->config)->toHaveKey(Assertion::INTERNAL_CONFIG_KEY);
});

test('toPassPython creates a python assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $code = 'return len(output) > 10';

    $result = $testCase->toPassPython($code);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(1);

    $assertion = $testCase->build()->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('python')
        ->and($assertion->value)->toBe($code);
});

test('toPassPython accepts threshold parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $code = 'return len(output) / 100';

    $testCase->toPassPython($code, threshold: 0.5);

    $assertion = $testCase->build()->assertions()[0];
    expect($assertion->threshold)->toBe(0.5);
});

test('toPassPython accepts config parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $code = 'return len(output) > config["minLength"]';
    $config = ['minLength' => 10];

    $testCase->toPassPython($code, config: $config);

    $assertion = $testCase->build()->assertions()[0];
    // User config is preserved alongside internal assertion_id
    expect($assertion->config)->toHaveKey('minLength')
        ->and($assertion->config['minLength'])->toBe(10)
        ->and($assertion->config)->toHaveKey(Assertion::INTERNAL_CONFIG_KEY);
});

test('toPassWebhook creates a webhook assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $url = 'https://example.com/validate';

    $result = $testCase->toPassWebhook($url);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(1);

    $assertion = $testCase->build()->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('webhook')
        ->and($assertion->value)->toBe($url);
});
