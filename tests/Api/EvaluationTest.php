<?php

declare(strict_types=1);

use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\Api\TestCase;

test('it can be instantiated with prompts', function () {
    $prompts = ['prompt1', 'prompt2', 'prompt3'];
    $evaluation = new Evaluation($prompts);

    expect($evaluation)->toBeInstanceOf(Evaluation::class);
});

test('it can be instantiated with empty prompts array', function () {
    $evaluation = new Evaluation([]);

    expect($evaluation)->toBeInstanceOf(Evaluation::class);
});

test('describe method sets description and returns self', function () {
    $evaluation = new Evaluation(['prompt1']);
    $description = 'Test description';

    $result = $evaluation->describe($description);

    expect($result)->toBe($evaluation);
});

test('describe method can be chained', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->describe('First description')
        ->describe('Second description');

    expect($result)->toBe($evaluation);
});

test('usingProvider method adds a single provider and returns self', function () {
    $evaluation = new Evaluation(['prompt1']);
    $provider = 'openai:gpt-4';

    $result = $evaluation->usingProvider($provider);

    expect($result)->toBe($evaluation);
});

test('usingProvider method can add multiple providers', function () {
    $evaluation = new Evaluation(['prompt1']);
    $provider1 = 'openai:gpt-4';
    $provider2 = 'anthropic:claude-3';
    $provider3 = 'google:gemini';

    $result = $evaluation->usingProvider($provider1, $provider2, $provider3);

    expect($result)->toBe($evaluation);
});

test('usingProvider method can be called multiple times', function () {
    $evaluation = new Evaluation(['prompt1']);

    $evaluation->usingProvider('openai:gpt-4');
    $result = $evaluation->usingProvider('anthropic:claude-3');

    expect($result)->toBe($evaluation);
});

test('usingProvider method can be chained', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->usingProvider('openai:gpt-4')
        ->usingProvider('anthropic:claude-3')
        ->usingProvider('google:gemini');

    expect($result)->toBe($evaluation);
});

test('expect method creates and returns a TestCase', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];

    $testCase = $evaluation->expect($variables);

    expect($testCase)->toBeInstanceOf(TestCase::class);
    expect($testCase->variables())->toBe($variables);
});

test('expect method can be called with no parameters', function () {
    $evaluation = new Evaluation(['prompt1']);

    $testCase = $evaluation->expect();

    expect($testCase)->toBeInstanceOf(TestCase::class);
    expect($testCase->variables())->toBe([]);
});

test('expect method can be called with empty variables array', function () {
    $evaluation = new Evaluation(['prompt1']);

    $testCase = $evaluation->expect([]);

    expect($testCase)->toBeInstanceOf(TestCase::class);
    expect($testCase->variables())->toBe([]);
});

test('expect method can create multiple test cases', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables1 = ['key1' => 'value1'];
    $variables2 = ['key2' => 'value2'];
    $variables3 = ['key3' => 'value3'];

    $testCase1 = $evaluation->expect($variables1);
    $testCase2 = $evaluation->expect($variables2);
    $testCase3 = $evaluation->expect($variables3);

    expect($testCase1)->toBeInstanceOf(TestCase::class);
    expect($testCase2)->toBeInstanceOf(TestCase::class);
    expect($testCase3)->toBeInstanceOf(TestCase::class);
    expect($testCase1)->not->toBe($testCase2);
    expect($testCase2)->not->toBe($testCase3);
    expect($testCase1->variables())->toBe($variables1);
    expect($testCase2->variables())->toBe($variables2);
    expect($testCase3->variables())->toBe($variables3);
});

test('clearTests method returns self', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->clearTests();

    expect($result)->toBe($evaluation);
});

test('clearTests method can be chained', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->clearTests()
        ->clearTests();

    expect($result)->toBe($evaluation);
});

test('methods can be chained together', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->describe('Test description')
        ->usingProvider('openai:gpt-4')
        ->expect(['key' => 'value'])
        ->toContain('test');

    expect($result)->toBeInstanceOf(TestCase::class);
});

test('expect method works after clearTests', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables1 = ['key1' => 'value1'];
    $variables2 = ['key2' => 'value2'];

    $testCase1 = $evaluation->expect($variables1);
    $evaluation->clearTests();
    $testCase2 = $evaluation->expect($variables2);

    expect($testCase1)->toBeInstanceOf(TestCase::class);
    expect($testCase2)->toBeInstanceOf(TestCase::class);
    expect($testCase2)->not->toBe($testCase1);
    expect($testCase2->variables())->toBe($variables2);
});
