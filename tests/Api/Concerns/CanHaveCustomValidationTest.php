<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toPassJavascript creates a javascript assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $code = 'return output.length > 10;';

    $result = $testCase->toPassJavascript($code);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('javascript')
        ->and($assertion->value)->toBe($code)
        ->and($assertion->options)->toHaveKey('code')
        ->and($assertion->options['code'])->toBe($code);
});

test('toPassPython creates a python assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $code = 'return len(output) > 10';

    $result = $testCase->toPassPython($code);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('python')
        ->and($assertion->value)->toBe($code)
        ->and($assertion->options)->toHaveKey('code')
        ->and($assertion->options['code'])->toBe($code);
});

test('toPassWebhook creates a webhook assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $url = 'https://example.com/validate';

    $result = $testCase->toPassWebhook($url);

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('webhook')
        ->and($assertion->value)->toBe($url)
        ->and($assertion->options)->toHaveKey('url')
        ->and($assertion->options['url'])->toBe($url);
});

test('custom validation methods accept options parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $options = ['custom' => 'value'];

    $testCase->toPassJavascript('code', $options);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('custom')
        ->and($assertion->options)->toHaveKey('code');
});

