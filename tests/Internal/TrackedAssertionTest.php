<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Helpers\SourceLocation;
use KevinPijning\Prompt\Internal\TrackedAssertion;

test('constructor sets assertion and sourceLocation properties', function () {
    $assertion = new Assertion('contains', 'test');
    $location = new SourceLocation('/path/to/test.php', 42);

    $tracked = new TrackedAssertion($assertion, $location);

    expect($tracked->assertion)->toBe($assertion)
        ->and($tracked->sourceLocation)->toBe($location);
});

test('constructor accepts null sourceLocation', function () {
    $assertion = new Assertion('contains', 'test');

    $tracked = new TrackedAssertion($assertion);

    expect($tracked->assertion)->toBe($assertion)
        ->and($tracked->sourceLocation)->toBeNull();
});

test('matches returns true for same type and value', function () {
    $assertion1 = new Assertion('contains', 'test');
    $assertion2 = new Assertion('contains', 'test');

    $tracked = new TrackedAssertion($assertion1);

    expect($tracked->matches($assertion2))->toBeTrue();
});

test('matches returns false for different type', function () {
    $assertion1 = new Assertion('contains', 'test');
    $assertion2 = new Assertion('equals', 'test');

    $tracked = new TrackedAssertion($assertion1);

    expect($tracked->matches($assertion2))->toBeFalse();
});

test('matches returns false for different value', function () {
    $assertion1 = new Assertion('contains', 'test');
    $assertion2 = new Assertion('contains', 'different');

    $tracked = new TrackedAssertion($assertion1);

    expect($tracked->matches($assertion2))->toBeFalse();
});

test('matches handles negated types correctly', function () {
    $assertion1 = new Assertion('not-contains', 'test');
    $assertion2 = new Assertion('not-contains', 'test');

    $tracked = new TrackedAssertion($assertion1);

    expect($tracked->matches($assertion2))->toBeTrue();
});
