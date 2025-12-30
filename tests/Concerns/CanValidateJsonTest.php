<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\TestCase;

describe('toEqualJson', function () {
    test('creates a javascript assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase->toEqualJson(['name' => 'John', 'age' => 30]);

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(1);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion)->toBeInstanceOf(Assertion::class)
            ->and($assertion->type)->toBe('javascript')
            ->and($assertion->value)->toContain('deepEqual');
    });

    test('includes expected JSON in the assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);
        $expected = ['name' => 'John', 'age' => 30];

        $testCase->toEqualJson($expected);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"name":"John"')
            ->and($assertion->value)->toContain('"age":30');
    });

    test('can be negated', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->not->toEqualJson(['name' => 'John']);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->type)->toBe('not-javascript');
    });

    test('handles nested structures', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);
        $expected = [
            'user' => [
                'name' => 'John',
                'address' => [
                    'city' => 'Amsterdam',
                ],
            ],
        ];

        $testCase->toEqualJson($expected);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('Amsterdam')
            ->and($assertion->value)->toContain('address');
    });

    test('handles arrays', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);
        $expected = ['items' => [1, 2, 3]];

        $testCase->toEqualJson($expected);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('[1,2,3]');
    });

    test('handles empty arrays', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toEqualJson(['items' => []]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"items":[]');
    });
});

describe('toMatchJsonStructure', function () {
    test('creates a javascript assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase->toMatchJsonStructure(['name', 'age']);

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(1);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion)->toBeInstanceOf(Assertion::class)
            ->and($assertion->type)->toBe('javascript')
            ->and($assertion->value)->toContain('checkStructure');
    });

    test('includes structure keys in the assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toMatchJsonStructure(['name', 'age', 'email']);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"name"')
            ->and($assertion->value)->toContain('"age"')
            ->and($assertion->value)->toContain('"email"');
    });

    test('handles nested structure', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toMatchJsonStructure([
            'name',
            'address' => ['city', 'street'],
        ]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"address"')
            ->and($assertion->value)->toContain('"city"')
            ->and($assertion->value)->toContain('"street"');
    });

    test('handles wildcard for arrays', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toMatchJsonStructure([
            'items' => [
                '*' => ['id', 'name'],
            ],
        ]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"*"')
            ->and($assertion->value)->toContain('"id"')
            ->and($assertion->value)->toContain('"name"');
    });

    test('can be negated', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->not->toMatchJsonStructure(['forbidden_key']);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->type)->toBe('not-javascript');
    });
});

describe('toHaveJsonFragment', function () {
    test('creates a javascript assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase->toHaveJsonFragment(['name' => 'John']);

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(1);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion)->toBeInstanceOf(Assertion::class)
            ->and($assertion->type)->toBe('javascript')
            ->and($assertion->value)->toContain('containsFragment');
    });

    test('includes fragment in the assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonFragment(['name' => 'John', 'active' => true]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"name":"John"')
            ->and($assertion->value)->toContain('"active":true');
    });

    test('can be negated', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->not->toHaveJsonFragment(['secret' => 'value']);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->type)->toBe('not-javascript');
    });

    test('handles nested values', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonFragment([
            'address' => ['city' => 'Amsterdam'],
        ]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('Amsterdam');
    });

    test('handles boolean values', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonFragment(['active' => false]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"active":false');
    });
});

describe('toHaveJsonFragments', function () {
    test('creates a javascript assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase->toHaveJsonFragments([
            ['name' => 'John'],
            ['age' => 30],
        ]);

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(1);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion)->toBeInstanceOf(Assertion::class)
            ->and($assertion->type)->toBe('javascript');
    });

    test('includes all fragments in the assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonFragments([
            ['name' => 'John'],
            ['city' => 'Amsterdam'],
        ]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"name":"John"')
            ->and($assertion->value)->toContain('"city":"Amsterdam"');
    });

    test('can be negated', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->not->toHaveJsonFragments([
            ['secret' => 'value'],
        ]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->type)->toBe('not-javascript');
    });
});

describe('toHaveJsonPath', function () {
    test('creates a javascript assertion for path existence', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase->toHaveJsonPath('name');

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(1);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion)->toBeInstanceOf(Assertion::class)
            ->and($assertion->type)->toBe('javascript')
            ->and($assertion->value)->toContain('pathParts');
    });

    test('includes path in the assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('user.name');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"user"')
            ->and($assertion->value)->toContain('"name"');
    });

    test('with expected value creates comparison assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('name', 'John');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"John"')
            ->and($assertion->value)->toContain('hasExpected = true');
    });

    test('with null expected value creates comparison assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('deletedAt', null);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('hasExpected = true')
            ->and($assertion->value)->toContain('const expected = null');
    });

    test('handles dot notation', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('address.city.name');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["address","city","name"]');
    });

    test('can be negated', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->not->toHaveJsonPath('secret');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->type)->toBe('not-javascript');
    });

    test('supports numeric array indices', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('people.0.name');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["people","0","name"]')
            ->and($assertion->value)->toContain('parseInt(current, 10)');
    });

    test('supports numeric array indices with expected value', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('people.1.name', 'Jane');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["people","1","name"]')
            ->and($assertion->value)->toContain('"Jane"');
    });

    test('supports wildcard for all array items', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('people.*.name');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["people","*","name"]')
            ->and($assertion->value)->toContain("current === '*'")
            ->and($assertion->value)->toContain('flatMap');
    });

    test('supports wildcard with expected value', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('items.*.status', 'active');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["items","*","status"]')
            ->and($assertion->value)->toContain('"active"')
            ->and($assertion->value)->toContain('values.every');
    });

    test('supports deeply nested wildcards', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('data.users.*.addresses.*.city');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["data","users","*","addresses","*","city"]');
    });
});

describe('toHaveJsonPaths', function () {
    test('creates a javascript assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase->toHaveJsonPaths(['name', 'age']);

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(1);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion)->toBeInstanceOf(Assertion::class)
            ->and($assertion->type)->toBe('javascript');
    });

    test('with array of paths checks existence', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPaths(['name', 'age', 'address.city']);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"name"')
            ->and($assertion->value)->toContain('"age"')
            ->and($assertion->value)->toContain('"address.city"')
            ->and($assertion->value)->toContain('isAssociative = false');
    });

    test('with associative array checks values', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPaths([
            'name' => 'John',
            'age' => 30,
        ]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"name":"John"')
            ->and($assertion->value)->toContain('"age":30')
            ->and($assertion->value)->toContain('isAssociative = true');
    });

    test('can be negated', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->not->toHaveJsonPaths(['secret', 'password']);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->type)->toBe('not-javascript');
    });

    test('handles nested paths with values', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPaths([
            'user.name' => 'John',
            'user.address.city' => 'Amsterdam',
        ]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"user.name":"John"')
            ->and($assertion->value)->toContain('"user.address.city":"Amsterdam"');
    });

    test('supports wildcard paths', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPaths(['items.*.id', 'items.*.name']);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"items.*.id"')
            ->and($assertion->value)->toContain('"items.*.name"');
    });

    test('supports wildcard paths with expected values', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPaths([
            'items.*.type' => 'product',
        ]);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"items.*.type":"product"');
    });
});

describe('toHaveJsonType', function () {
    test('creates a javascript assertion', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase->toHaveJsonType('name', 'string');

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(1);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion)->toBeInstanceOf(Assertion::class)
            ->and($assertion->type)->toBe('javascript')
            ->and($assertion->value)->toContain('expectedType');
    });

    test('checks string type', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('name', 'string');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"string"');
    });

    test('checks number type', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('age', 'number');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"number"');
    });

    test('checks boolean type', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('active', 'boolean');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"boolean"');
    });

    test('checks array type', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('items', 'array');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"array"');
    });

    test('checks object type', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('address', 'object');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"object"');
    });

    test('checks null type', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('deletedAt', 'null');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('"null"');
    });

    test('handles dot notation', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('address.city', 'string');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["address","city"]');
    });

    test('can be negated', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->not->toHaveJsonType('age', 'string');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->type)->toBe('not-javascript');
    });

    test('supports numeric array indices', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('items.0.price', 'number');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["items","0","price"]')
            ->and($assertion->value)->toContain('"number"');
    });

    test('supports wildcard for all array items', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('people.*.age', 'number');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('["people","*","age"]')
            ->and($assertion->value)->toContain('"number"')
            ->and($assertion->value)->toContain('values.every');
    });

    test('with wildcard reports all non-matching types', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonType('items.*.value', 'string');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('actualTypes')
            ->and($assertion->value)->toContain('new Set');
    });
});

describe('chaining and integration', function () {
    test('json validation methods can be chained', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase
            ->toMatchJsonStructure(['name', 'age'])
            ->toHaveJsonFragment(['name' => 'John'])
            ->toHaveJsonPath('age', 30)
            ->toHaveJsonType('name', 'string');

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(4);

        $assertions = $testCase->build()->assertions;
        expect($assertions[0]->type)->toBe('javascript')
            ->and($assertions[1]->type)->toBe('javascript')
            ->and($assertions[2]->type)->toBe('javascript')
            ->and($assertions[3]->type)->toBe('javascript');
    });

    test('json validation methods can be mixed with other assertions', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $result = $testCase
            ->toBeJson()
            ->toHaveJsonPath('name')
            ->toContain('John');

        expect($result)->toBe($testCase)
            ->and($testCase->build()->assertions)->toHaveCount(3);

        $assertions = $testCase->build()->assertions;
        expect($assertions[0]->type)->toBe('is-json')
            ->and($assertions[1]->type)->toBe('javascript')
            ->and($assertions[2]->type)->toBe('icontains');
    });

    test('markdown code block handling is included in assertions', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toEqualJson(['test' => 'value']);

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('```(?:json)?');
    });

    test('error handling for invalid JSON is included in assertions', function () {
        $evaluation = new Evaluation(['prompt1']);
        $testCase = new TestCase([], $evaluation);

        $testCase->toHaveJsonPath('name');

        $assertion = $testCase->build()->assertions[0];
        expect($assertion->value)->toContain('Output is not valid JSON');
    });
});
