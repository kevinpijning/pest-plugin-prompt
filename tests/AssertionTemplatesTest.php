<?php

declare(strict_types=1);

use KevinPijning\Prompt\AssertionTemplates;
use KevinPijning\Prompt\TestContext;

beforeEach(function () {
    AssertionTemplates::resetForTests();
    AssertionTemplates::setTestPath(__DIR__.'/fixtures/prompt-assertion-templates.php');
});

afterEach(function () {
    AssertionTemplates::resetForTests();
    AssertionTemplates::setTestPath(null);
});

test('assertion templates are loaded and applied automatically', function () {
    $evaluation = prompt('Hello {{name}}');

    $templates = $evaluation->assertionTemplates();

    expect($templates)->toHaveKey('mentionsCoffee')
        ->and($templates['mentionsCoffee']->type)->toBe('icontains')
        ->and($templates['mentionsCoffee']->value)->toBe('coffee')
        ->and($templates)->toHaveKey('prefersLaravelOverNextJs')
        ->and($templates['prefersLaravelOverNextJs']->type)->toBe('llm-rubric')
        ->and($templates['prefersLaravelOverNextJs']->value)->toBe('The answer states a clear preference for Laravel over Next.js.')
        ->and($templates['prefersLaravelOverNextJs']->threshold)->toBe(0.9);
});

test('usingAssertionTemplates references loaded assertion templates', function () {
    $evaluation = prompt('Hello {{name}}');

    $evaluation->expect(['name' => 'Alice'])
        ->usingAssertionTemplates('mentionsCoffee', 'prefersLaravelOverNextJs');

    $assertions = $evaluation->testCases()[0]->assertions();

    expect($assertions[0]->templateName)->toBe('mentionsCoffee')
        ->and($assertions[1]->templateName)->toBe('prefersLaravelOverNextJs');

    // Prevent the global afterEach evaluation hook from invoking promptfoo in this unit test.
    TestContext::clear();
});

test('usingAssertionTemplates throws for missing template', function () {
    $evaluation = prompt('Hello {{name}}');

    expect(fn () => $evaluation->expect(['name' => 'Bob'])
        ->usingAssertionTemplates('doesNotExist'))
        ->toThrow(InvalidArgumentException::class, "Assertion template 'doesNotExist' was not found");

    TestContext::clear();
});
