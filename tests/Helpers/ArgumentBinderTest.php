<?php

declare(strict_types=1);

use KevinPijning\Prompt\Helpers\ArgumentBinder;
use KevinPijning\Prompt\TestCase;

test('binds named arguments by parameter name', function () {
    $callback = fn (TestCase $tc, string $word, int $count) => null;

    $result = ArgumentBinder::bind($callback, ['word' => 'hello', 'count' => 5]);

    expect($result)->toBe(['hello', 5]);
});

test('binds named arguments in any order', function () {
    $callback = fn (TestCase $tc, string $first, string $second) => null;

    $result = ArgumentBinder::bind($callback, ['second' => 'world', 'first' => 'hello']);

    expect($result)->toBe(['hello', 'world']);
});

test('binds positional arguments by position', function () {
    $callback = fn (TestCase $tc, string $first, string $second) => null;

    $result = ArgumentBinder::bind($callback, ['hello', 'world']);

    expect($result)->toBe(['hello', 'world']);
});

test('uses default values for missing named arguments', function () {
    $callback = fn (TestCase $tc, string $word, string $suffix = '!') => null;

    $result = ArgumentBinder::bind($callback, ['word' => 'hello']);

    expect($result)->toBe(['hello', '!']);
});

test('throws for missing required named argument', function () {
    $callback = fn (TestCase $tc, string $word) => null;

    ArgumentBinder::bind($callback, ['other' => 'value']);
})->throws(InvalidArgumentException::class, "Missing required argument 'word'");

test('handles empty argument array', function () {
    $callback = fn (TestCase $tc) => null;

    $result = ArgumentBinder::bind($callback, []);

    expect($result)->toBe([]);
});

test('handles nullable parameters with null value', function () {
    $callback = fn (TestCase $tc, ?string $word) => null;

    $result = ArgumentBinder::bind($callback, [null]);

    expect($result)->toBe([null]);
});
