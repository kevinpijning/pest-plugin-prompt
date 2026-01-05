<?php

declare(strict_types=1);

use KevinPijning\Prompt\Concerns\CanBeExtended;

beforeEach(function (): void {
    TestExtendableClass::flushExtensions();
});

test('extend registers a new method', function (): void {
    TestExtendableClass::extend('customMethod', fn () => 'custom result');

    expect(TestExtendableClass::hasExtension('customMethod'))->toBeTrue();
});

test('hasExtension returns false for unregistered methods', function (): void {
    expect(TestExtendableClass::hasExtension('nonExistent'))->toBeFalse();
});

test('flushExtensions removes all extensions', function (): void {
    TestExtendableClass::extend('method1', fn () => 'one');
    TestExtendableClass::extend('method2', fn () => 'two');

    TestExtendableClass::flushExtensions();

    expect(TestExtendableClass::hasExtension('method1'))->toBeFalse();
    expect(TestExtendableClass::hasExtension('method2'))->toBeFalse();
});

test('__call invokes registered extension', function (): void {
    TestExtendableClass::extend('greet', fn (TestExtendableClass $self, string $name) => "Hello, {$name}!");

    $instance = new TestExtendableClass;
    $result = $instance->greet('World');

    expect($result)->toBe('Hello, World!');
});

test('__call returns $this when extension returns null', function (): void {
    TestExtendableClass::extend('chainable', function (TestExtendableClass $self): void {});

    $instance = new TestExtendableClass;
    $result = $instance->chainable();

    expect($result)->toBe($instance);
});

test('__call throws BadMethodCallException for unknown method', function (): void {
    $instance = new TestExtendableClass;

    expect(fn () => $instance->unknownMethod())
        ->toThrow(BadMethodCallException::class, 'Method TestExtendableClass::unknownMethod does not exist.');
});

test('extension has access to $this', function (): void {
    TestExtendableClass::extend('getValue', fn (TestExtendableClass $self) => $self->value);

    $instance = new TestExtendableClass;
    $instance->value = 'test value';

    expect($instance->getValue())->toBe('test value');
});

test('extension can modify instance properties', function (): void {
    TestExtendableClass::extend('setValue', function (TestExtendableClass $self, string $value): void {
        $self->value = $value;
    });

    $instance = new TestExtendableClass;
    $instance->setValue('modified');

    expect($instance->value)->toBe('modified');
});

test('extensions are isolated per class', function (): void {
    TestExtendableClass::extend('classOneMethod', fn () => 'class one');
    AnotherExtendableClass::extend('classTwoMethod', fn () => 'class two');

    expect(TestExtendableClass::hasExtension('classOneMethod'))->toBeTrue();
    expect(TestExtendableClass::hasExtension('classTwoMethod'))->toBeFalse();

    expect(AnotherExtendableClass::hasExtension('classTwoMethod'))->toBeTrue();
    expect(AnotherExtendableClass::hasExtension('classOneMethod'))->toBeFalse();
});

test('extension can accept multiple arguments', function (): void {
    TestExtendableClass::extend('combine', fn (TestExtendableClass $self, string $a, string $b, string $c) => "{$a}-{$b}-{$c}");

    $instance = new TestExtendableClass;

    expect($instance->combine('one', 'two', 'three'))->toBe('one-two-three');
});

test('extension can return various types', function (): void {
    TestExtendableClass::extend('returnArray', fn (TestExtendableClass $self) => ['a', 'b', 'c']);
    TestExtendableClass::extend('returnInt', fn (TestExtendableClass $self) => 42);
    TestExtendableClass::extend('returnBool', fn (TestExtendableClass $self) => true);

    $instance = new TestExtendableClass;

    expect($instance->returnArray())->toBe(['a', 'b', 'c']);
    expect($instance->returnInt())->toBe(42);
    expect($instance->returnBool())->toBe(true);
});

test('extension can transform array argument', function (): void {
    TestExtendableClass::extend('appendItems', fn (TestExtendableClass $self, array $input) => [...$input, 'a', 'b', 'c']);

    $instance = new TestExtendableClass;

    expect($instance->appendItems(['x', 'y']))->toBe(['x', 'y', 'a', 'b', 'c']);
});

class TestExtendableClass
{
    use CanBeExtended;

    public string $value = '';
}

class AnotherExtendableClass
{
    use CanBeExtended;
}
