<?php

declare(strict_types=1);

use KevinPijning\Prompt\AssertionGroup;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('assertion group can be instantiated without callback', function () {
    $group = new AssertionGroup;

    expect($group)->toBeInstanceOf(AssertionGroup::class);
});

test('assertion group can be instantiated with callback', function () {
    $callback = fn (TestCase $tc) => $tc->toContain('hello');
    $group = new AssertionGroup($callback);

    expect($group)->toBeInstanceOf(AssertionGroup::class);
});

test('assertion group can collect assertions via fluent methods', function () {
    $group = new AssertionGroup;

    $group->toContain('hello')
        ->toContain('world');

    expect($group)->toBeInstanceOf(AssertionGroup::class);
});

test('assertion group applies fluent assertions to test case', function () {
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    $group = new AssertionGroup;
    $group->toContain('hello')
        ->toContain('world');

    $group->apply($testCase);

    expect($testCase->build()->assertions)->toHaveCount(2)
        ->and($testCase->build()->assertions[0]->type)->toBe('icontains')
        ->and($testCase->build()->assertions[0]->value)->toBe('hello')
        ->and($testCase->build()->assertions[1]->type)->toBe('icontains')
        ->and($testCase->build()->assertions[1]->value)->toBe('world');
});

test('assertion group applies callback to test case', function () {
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    $group = new AssertionGroup(function (TestCase $tc) {
        $tc->toContain('hello')
            ->toBeJudged('friendly');
    });

    $group->apply($testCase);

    expect($testCase->build()->assertions)->toHaveCount(2)
        ->and($testCase->build()->assertions[0]->type)->toBe('icontains')
        ->and($testCase->build()->assertions[0]->value)->toBe('hello')
        ->and($testCase->build()->assertions[1]->type)->toBe('llm-rubric')
        ->and($testCase->build()->assertions[1]->value)->toBe('friendly');
});

test('assertion group callback receives test case as first parameter', function () {
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();
    $receivedTestCase = null;

    $group = new AssertionGroup(function (TestCase $tc) use (&$receivedTestCase) {
        $receivedTestCase = $tc;
    });

    $group->apply($testCase);

    expect($receivedTestCase)->toBe($testCase);
});

test('assertion group with callback and extra parameters', function () {
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    $group = new AssertionGroup(function (TestCase $tc, string $word) {
        $tc->toContain($word);
    });

    $group->apply($testCase, ['word' => 'hello']);

    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->value)->toBe('hello');
});

test('assertion group supports positional arguments', function () {
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    $group = new AssertionGroup(function (TestCase $tc, string $first, string $second) {
        $tc->toContain($first)
            ->toContain($second);
    });

    $group->apply($testCase, ['hello', 'world']);

    expect($testCase->build()->assertions)->toHaveCount(2)
        ->and($testCase->build()->assertions[0]->value)->toBe('hello')
        ->and($testCase->build()->assertions[1]->value)->toBe('world');
});

test('assertion group supports optional parameters with defaults', function () {
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    $group = new AssertionGroup(function (TestCase $tc, string $word = 'default') {
        $tc->toContain($word);
    });

    $group->apply($testCase, []);

    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->value)->toBe('default');
});

test('assertion group supports nullable parameters', function () {
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    $group = new AssertionGroup(function (TestCase $tc, ?string $word) {
        if ($word !== null) {
            $tc->toContain($word);
        }
    });

    $group->apply($testCase, [null]);

    expect($testCase->build()->assertions)->toHaveCount(0);
});

test('assertion group can use multiple assertion traits', function () {
    $group = new AssertionGroup;

    $group->toContain('hello')
        ->toBeJudged('friendly')
        ->toEqual('exact')
        ->toBeSimilar('similar text');

    expect($group)->toBeInstanceOf(AssertionGroup::class);
});
