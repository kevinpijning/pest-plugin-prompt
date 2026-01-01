<?php

declare(strict_types=1);

use KevinPijning\Prompt\AssertionGroup;
use KevinPijning\Prompt\Internal\AssertionGroupContext;

beforeEach(function () {
    // Nothing to clear explicitly; contexts are static and tests are simple.
});

test('PromptAssertionGroupContext can add and retrieve an assertion group', function () {
    $group = new AssertionGroup('be nice');

    $result = AssertionGroupContext::add('be nice', $group);

    expect($result)->toBe($group)
        ->and(AssertionGroupContext::has('be nice'))->toBeTrue()
        ->and(AssertionGroupContext::get('be nice'))->toBe($group);
});

test('PromptAssertionGroupContext can store multiple assertion groups with different names', function () {
    $group1 = new AssertionGroup('be nice');
    $group2 = new AssertionGroup('be professional');

    AssertionGroupContext::add('be nice', $group1);
    AssertionGroupContext::add('be professional', $group2);

    expect(AssertionGroupContext::has('be nice'))->toBeTrue()
        ->and(AssertionGroupContext::has('be professional'))->toBeTrue()
        ->and(AssertionGroupContext::get('be nice'))->toBe($group1)
        ->and(AssertionGroupContext::get('be professional'))->toBe($group2);
});

test('PromptAssertionGroupContext overwrites assertion groups with the same name', function () {
    $group1 = new AssertionGroup('be nice');
    $group2 = new AssertionGroup('be nice');

    AssertionGroupContext::add('be nice', $group1);
    expect(AssertionGroupContext::get('be nice'))->toBe($group1);

    AssertionGroupContext::add('be nice', $group2);

    expect(AssertionGroupContext::get('be nice'))->toBe($group2)
        ->and(AssertionGroupContext::get('be nice'))->not->toBe($group1);
});
