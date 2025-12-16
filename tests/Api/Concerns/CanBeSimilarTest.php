<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toBeSimilar creates a similar assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeSimilar('expected text');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('similar')
        ->and($assertion->value)->toBe('expected text');
});

test('toBeSimilar accepts array of expected values', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $expected = ['text1', 'text2'];

    $testCase->toBeSimilar($expected);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe($expected);
});

test('toBeSimilar accepts threshold and provider', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toBeSimilar('text', threshold: 0.8, provider: 'huggingface:model');

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->threshold)->toBe(0.8)
        ->and($assertion->options)->toHaveKey('provider')
        ->and($assertion->options['provider'])->toBe('huggingface:model');
});

test('toHaveLevenshtein creates a levenshtein assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveLevenshtein('expected', threshold: 2.0);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('levenshtein')
        ->and($assertion->value)->toBe('expected')
        ->and($assertion->threshold)->toBe(2.0);
});

test('toHaveRougeN creates a rouge-n assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveRougeN(1, 'expected', threshold: 0.8);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('rouge-n')
        ->and($assertion->value)->toBe('expected')
        ->and($assertion->threshold)->toBe(0.8)
        ->and($assertion->options)->toHaveKey('n')
        ->and($assertion->options['n'])->toBe(1);
});

test('toHaveFScore creates an f-score assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHaveFScore('expected', threshold: 0.9);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('f-score')
        ->and($assertion->value)->toBe('expected')
        ->and($assertion->threshold)->toBe(0.9);
});

test('toHavePerplexity creates a perplexity assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHavePerplexity(threshold: 10.0);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('perplexity')
        ->and($assertion->threshold)->toBe(10.0);
});

test('toHavePerplexityScore creates a perplexity-score assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toHavePerplexityScore(threshold: 0.5);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('perplexity-score')
        ->and($assertion->threshold)->toBe(0.5);
});
