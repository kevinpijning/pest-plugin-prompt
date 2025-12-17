<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toContain creates an icontains assertion by default', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toContain('test');

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains')
        ->and($assertion->value)->toBe('test');
});

test('toContain creates a contains assertion when strict is true', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $testCase->toContain('test', strict: true);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('contains');
});

test('toContainAll creates an assertion with default parameters', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = ['test1', 'test2'];

    $result = $testCase->toContainAll($contains);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains-all')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBeNull();
});

test('toContainAll creates an icontains-all assertion when strict is false', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = ['test1', 'test2'];

    $testCase->toContainAll($contains, strict: false);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains-all')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBeNull();
});

test('toContainAll creates a contains-all assertion when strict is true', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = ['test1', 'test2'];

    $testCase->toContainAll($contains, strict: true);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('contains-all')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBeNull();
});

test('toContainAll can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toContainAll(['first', 'second'])
        ->toContainAll(['third', 'fourth']);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(2);

    $assertions = $testCase->build()->assertions;
    expect($assertions[0]->value)->toBe(['first', 'second'])
        ->and($assertions[1]->value)->toBe(['third', 'fourth'])
        ->and($assertions[0]->type)->toBe('icontains-all')
        ->and($assertions[1]->type)->toBe('icontains-all');
});

test('toContainAny creates an assertion with default parameters', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = ['test1', 'test2'];

    $result = $testCase->toContainAny($contains);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains-any')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBeNull();
});

test('toContainAny creates an icontains-any assertion when strict is false', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = ['test1', 'test2'];

    $testCase->toContainAny($contains, strict: false);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('icontains-any')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBeNull();
});

test('toContainAny creates a contains-any assertion when strict is true', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $contains = ['test1', 'test2'];

    $testCase->toContainAny($contains, strict: true);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('contains-any')
        ->and($assertion->value)->toBe($contains)
        ->and($assertion->threshold)->toBeNull();
});

test('toContainAny can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toContainAny(['first', 'second'])
        ->toContainAny(['third', 'fourth']);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(2);

    $assertions = $testCase->build()->assertions;
    expect($assertions[0]->value)->toBe(['first', 'second'])
        ->and($assertions[1]->value)->toBe(['third', 'fourth'])
        ->and($assertions[0]->type)->toBe('icontains-any')
        ->and($assertions[1]->type)->toBe('icontains-any');
});

test('toContainJson creates a contains-json assertion', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->toContainJson();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('contains-json')
        ->and($assertion->value)->toBeNull()
        ->and($assertion->threshold)->toBeNull();
});

test('toContainJson accepts schema parameter', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $schema = ['type' => 'object', 'properties' => ['name' => ['type' => 'string']]];

    $testCase->toContainJson($schema);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe($schema);
});

test('toContainHtml creates a contains-html assertion', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->toContainHtml();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('contains-html')
        ->and($assertion->value)->toBeNull()
        ->and($assertion->threshold)->toBeNull();
});

test('toContainSql creates a contains-sql assertion', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->toContainSql();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('contains-sql')
        ->and($assertion->value)->toBeNull()
        ->and($assertion->threshold)->toBeNull();
});

test('toContainSql accepts config parameter with databaseType', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $config = ['databaseType' => 'mysql'];

    $testCase->toContainSql($config);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe($config);
});

test('toContainXml creates a contains-xml assertion', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->toContainXml();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('contains-xml')
        ->and($assertion->value)->toBeNull()
        ->and($assertion->threshold)->toBeNull();
});

test('toContainXml accepts config parameter with requiredElements', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $config = ['requiredElements' => ['root.child', 'root.sibling']];

    $testCase->toContainXml($config);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe($config);
});

test('format-specific assertions can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toContainJson()
        ->toContainHtml()
        ->toContainSql()
        ->toContainXml();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(4);

    $assertions = $testCase->build()->assertions;
    expect($assertions[0]->type)->toBe('contains-json')
        ->and($assertions[1]->type)->toBe('contains-html')
        ->and($assertions[2]->type)->toBe('contains-sql')
        ->and($assertions[3]->type)->toBe('contains-xml');
});

test('all CanContain methods can be mixed and chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->toContain('single')
        ->toContainAll(['all1', 'all2'])
        ->toContainAny(['any1', 'any2'])
        ->toContainJson()
        ->toContainHtml()
        ->toContainSql()
        ->toContainXml();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(7);

    $assertions = $testCase->build()->assertions;
    expect($assertions[0]->type)->toBe('icontains')
        ->and($assertions[1]->type)->toBe('icontains-all')
        ->and($assertions[2]->type)->toBe('icontains-any')
        ->and($assertions[3]->type)->toBe('contains-json')
        ->and($assertions[4]->type)->toBe('contains-html')
        ->and($assertions[5]->type)->toBe('contains-sql')
        ->and($assertions[6]->type)->toBe('contains-xml');
});
