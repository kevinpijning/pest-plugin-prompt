<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

test('toBeJson creates an is-json assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeJson();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('is-json')
        ->and($assertion->value)->toBeNull();
});

test('toBeJson accepts schema parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $schema = ['type' => 'object', 'properties' => ['name' => ['type' => 'string']]];

    $testCase->toBeJson($schema);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe($schema);
});

test('toBeHtml creates an is-html assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeHtml();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('is-html');
});

test('toBeSql creates an is-sql assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeSql();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('is-sql');
});

test('toBeSql accepts config parameter with databaseType', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $config = ['databaseType' => 'mysql'];

    $testCase->toBeSql($config);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe($config);
});

test('toBeXml creates an is-xml assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeXml();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(1);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->type)->toBe('is-xml');
});

test('toBeXml accepts config parameter with requiredElements', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $config = ['requiredElements' => ['root.child', 'root.sibling']];

    $testCase->toBeXml($config);

    $assertion = $testCase->build()->assertions[0];
    expect($assertion->value)->toBe($config);
});

test('can chain validation methods', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase
        ->toBeJson()
        ->toBeHtml()
        ->toBeSql()
        ->toBeXml();

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions)->toHaveCount(4);
});
