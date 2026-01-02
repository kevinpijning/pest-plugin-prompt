<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\EvaluationRegistry;
use KevinPijning\Prompt\TestCase;

test('to method executes callback with test case and returns self', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];
    $testCase = new TestCase($variables, $evaluation);
    $callbackExecuted = false;
    $receivedTestCase = null;

    $result = $testCase->to(function (TestCase $tc) use (&$callbackExecuted, &$receivedTestCase) {
        $callbackExecuted = true;
        $receivedTestCase = $tc;
    });

    expect($callbackExecuted)->toBeTrue()
        ->and($receivedTestCase)->toBe($testCase)
        ->and($result)->toBe($testCase);
});

test('to method can be used to add assertions', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->to(function (TestCase $tc) {
        $tc->toContain('test')
            ->toContain('value');
    });

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(2)
        ->and($testCase->build()->assertions()[0])->toBeInstanceOf(Assertion::class)
        ->and($testCase->build()->assertions()[1])->toBeInstanceOf(Assertion::class);
});

test('to method can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->to(fn (TestCase $tc) => $tc->toContain('first'))
        ->to(fn (TestCase $tc) => $tc->toContain('second'));

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(2);
});

test('group method is an alias for to method', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);
    $callbackExecuted = false;

    $result = $testCase->group(function (TestCase $tc) use (&$callbackExecuted) {
        $callbackExecuted = true;
        $tc->toContain('test');
    });

    expect($callbackExecuted)->toBeTrue()
        ->and($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(1);
});

test('group method can be chained', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->group(fn (TestCase $tc) => $tc->toContain('first'))
        ->group(fn (TestCase $tc) => $tc->toContain('second'));

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(2);
});

test('to and group methods can be mixed', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase
        ->to(fn (TestCase $tc) => $tc->toContain('first'))
        ->group(fn (TestCase $tc) => $tc->toContain('second'))
        ->to(fn (TestCase $tc) => $tc->toContain('third'));

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(3);
});

test('to method accepts invokable class instance', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->to(new InvokableTestClass);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(1)
        ->and($testCase->build()->assertions()[0]->type)->toBe('icontains')
        ->and($testCase->build()->assertions()[0]->value)->toBe('invokable');
});

test('group method accepts invokable class instance', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $variables = ['key1' => 'value1'];
    $testCase = new TestCase($variables, $evaluation);

    $result = $testCase->group(new InvokableTestClass);

    expect($result)->toBe($testCase)
        ->and($testCase->build()->assertions())->toHaveCount(1)
        ->and($testCase->build()->assertions()[0]->type)->toBe('icontains')
        ->and($testCase->build()->assertions()[0]->value)->toBe('invokable');
});

test('to method throws exception for unknown string that is not a named group', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $testCase = new TestCase([], $evaluation);

    $testCase->to('unknown group');
})->throws(InvalidArgumentException::class, "Assertion group 'unknown group' not found. Register it using assertion().");

test('to method can use named assertion group without arguments', function () {
    EvaluationRegistry::clear();

    assertion('be nice')
        ->toContain('hello')
        ->toBeJudged('friendly');

    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->to('be nice');

    $built = $testCase->build();

    expect($result)->toBe($testCase)
        ->and($built->assertions())->toHaveCount(2)
        ->and($built->assertions()[0]->type)->toBe('icontains')
        ->and($built->assertions()[0]->value)->toBe('hello')
        ->and($built->assertions()[1]->type)->toBe('llm-rubric')
        ->and($built->assertions()[1]->value)->toBe('friendly');
});

test('to method can use named assertion group with arguments', function () {
    EvaluationRegistry::clear();

    assertion('be kind', function (TestCase $tc, string $word): void {
        $tc->toContain($word)
            ->toBeJudged("be {$word}");
    });

    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->to('be kind', ['word' => 'gentle']);

    $built = $testCase->build();

    expect($result)->toBe($testCase)
        ->and($built->assertions())->toHaveCount(2)
        ->and($built->assertions()[0]->value)->toBe('gentle')
        ->and($built->assertions()[1]->value)->toBe('be gentle');
});

test('group method can use named assertion group', function () {
    EvaluationRegistry::clear();

    assertion('be polite')
        ->toContain('please');

    $evaluation = new Evaluation(['prompt1']);
    $testCase = new TestCase([], $evaluation);

    $result = $testCase->group('be polite');

    $built = $testCase->build();

    expect($result)->toBe($testCase)
        ->and($built->assertions())->toHaveCount(1)
        ->and($built->assertions()[0]->type)->toBe('icontains')
        ->and($built->assertions()[0]->value)->toBe('please');
});

class InvokableTestClass
{
    public function __invoke(TestCase $testCase): void
    {
        $testCase->toContain('invokable');
    }
}
