<?php

declare(strict_types=1);

use Pest\Prompt\Promptfoo\Results\TestCase;

test('TestCase can be instantiated with all properties', function () {
    $testCase = new TestCase(
        vars: ['message' => 'Hello World!', 'language' => 'es'],
        assert: [
            ['type' => 'icontains', 'value' => 'Hola'],
            ['type' => 'icontains', 'value' => 'muda'],
        ],
        options: ['key' => 'value'],
        metadata: ['meta' => 'data'],
    );

    expect($testCase->vars)->toBeArray()
        ->and($testCase->vars['message'])->toBe('Hello World!')
        ->and($testCase->vars['language'])->toBe('es')
        ->and($testCase->assert)->toBeArray()
        ->and($testCase->assert)->toHaveCount(2)
        ->and($testCase->options)->toBeArray()
        ->and($testCase->options['key'])->toBe('value')
        ->and($testCase->metadata)->toBeArray()
        ->and($testCase->metadata['meta'])->toBe('data');
});

test('TestCase can be instantiated with empty arrays', function () {
    $testCase = new TestCase(
        vars: [],
        assert: [],
        options: [],
        metadata: [],
    );

    expect($testCase->vars)->toBeArray()
        ->and($testCase->vars)->toBeEmpty()
        ->and($testCase->assert)->toBeArray()
        ->and($testCase->assert)->toBeEmpty()
        ->and($testCase->options)->toBeArray()
        ->and($testCase->options)->toBeEmpty()
        ->and($testCase->metadata)->toBeArray()
        ->and($testCase->metadata)->toBeEmpty();
});
