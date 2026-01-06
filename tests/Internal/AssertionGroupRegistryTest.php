<?php

declare(strict_types=1);

use KevinPijning\Prompt\AssertionGroup;
use KevinPijning\Prompt\Internal\AssertionGroupRegistry;

beforeEach(function () {
    // Nothing to clear explicitly; contexts are static and tests are simple.
});

test('AssertionGroupRegistry can add and retrieve an assertion group', function () {
    $group = new AssertionGroup;

    $result = AssertionGroupRegistry::add('be nice', $group);

    expect($result)->toBe($group)
        ->and(AssertionGroupRegistry::has('be nice'))->toBeTrue()
        ->and(AssertionGroupRegistry::get('be nice'))->toBe($group);
});

test('AssertionGroupRegistry can store multiple assertion groups with different names', function () {
    $group1 = new AssertionGroup;
    $group2 = new AssertionGroup;

    AssertionGroupRegistry::add('be nice', $group1);
    AssertionGroupRegistry::add('be professional', $group2);

    expect(AssertionGroupRegistry::has('be nice'))->toBeTrue()
        ->and(AssertionGroupRegistry::has('be professional'))->toBeTrue()
        ->and(AssertionGroupRegistry::get('be nice'))->toBe($group1)
        ->and(AssertionGroupRegistry::get('be professional'))->toBe($group2);
});

test('AssertionGroupRegistry overwrites assertion groups with the same name', function () {
    $group1 = new AssertionGroup;
    $group2 = new AssertionGroup;

    AssertionGroupRegistry::add('be nice', $group1);
    expect(AssertionGroupRegistry::get('be nice'))->toBe($group1);

    AssertionGroupRegistry::add('be nice', $group2);

    expect(AssertionGroupRegistry::get('be nice'))->toBe($group2)
        ->and(AssertionGroupRegistry::get('be nice'))->not->toBe($group1);
});
