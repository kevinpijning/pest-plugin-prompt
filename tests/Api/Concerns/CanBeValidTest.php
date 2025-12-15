<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\TestCase;

test('toBeJson creates an is-json assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeJson();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion)->toBeInstanceOf(Assertion::class)
        ->and($assertion->type)->toBe('is-json')
        ->and($assertion->value)->toBeNull();
});

test('toBeJson accepts schema parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $schema = ['type' => 'object', 'properties' => ['name' => ['type' => 'string']]];

    $testCase->toBeJson($schema);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('schema')
        ->and($assertion->options['schema'])->toBe($schema);
});

test('toBeHtml creates an is-html assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeHtml();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion->type)->toBe('is-html');
});

test('toBeSql creates an is-sql assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeSql();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion->type)->toBe('is-sql');
});

test('toBeSql accepts authorityList parameter', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);
    $authorityList = ['SELECT', 'INSERT'];

    $testCase->toBeSql($authorityList);

    $assertion = $testCase->assertions()[0];
    expect($assertion->options)->toHaveKey('authorityList')
        ->and($assertion->options['authorityList'])->toBe($authorityList);
});

test('toBeXml creates an is-xml assertion', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->toBeXml();

    expect($result)->toBe($testCase)
        ->and($testCase->assertions())->toHaveCount(1);

    $assertion = $testCase->assertions()[0];
    expect($assertion->type)->toBe('is-xml');
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
        ->and($testCase->assertions())->toHaveCount(4);
});

