<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\EvaluationRegistry;
use KevinPijning\Prompt\Internal\EvaluationResult;
use KevinPijning\Prompt\Internal\Results\ComponentResult;
use KevinPijning\Prompt\Internal\Results\GradingResult;
use KevinPijning\Prompt\Internal\Results\Prompt;
use KevinPijning\Prompt\Internal\Results\Provider;
use KevinPijning\Prompt\Internal\Results\Response;
use KevinPijning\Prompt\Internal\Results\Result;
use KevinPijning\Prompt\Internal\Results\TestCase;
use KevinPijning\Prompt\Internal\TestLifecycle;

beforeEach(function () {
    EvaluationRegistry::clear();
});

test('evaluate skips evaluations with empty test cases', function () {
    $evaluation = new Evaluation(['test prompt']);
    // No test cases added

    EvaluationRegistry::addEvaluation($evaluation);

    // Should not throw and should clear context
    TestLifecycle::evaluate();

    expect(EvaluationRegistry::getCurrentEvaluations())->toBeEmpty();
});

// Note: We can't easily test evaluate() with real evaluations as it would try to execute promptfoo commands
// The evaluate() method is tested indirectly through integration tests. The key behavior (context clearing)
// is already covered by the "evaluate skips evaluations with empty test cases" test above.

test('handleEvaluationResult processes all results', function () {
    $result1 = new Result(
        cost: 0.1,
        error: null,
        gradingResult: new GradingResult(
            pass: true,
            score: 1.0,
            reason: '',
            namedScores: [],
            tokensUsed: [],
            componentResults: [
                new ComponentResult(
                    pass: true,
                    score: 1.0,
                    reason: 'Passed',
                    assertion: new Assertion('contains', 'test')
                ),
            ]
        ),
        id: 'id1',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test', 'test'),
        promptId: 'pid1',
        promptIdx: 0,
        provider: new Provider('provider1', ''),
        response: new Response('test output', [], false, 50, 'stop', 0.05, []),
        score: 1.0,
        success: true,
        testCase: new TestCase([], [], [], []),
        testIdx: 0,
        vars: [],
        metadata: [],
        failureReason: null
    );

    $result2 = new Result(
        cost: 0.1,
        error: null,
        gradingResult: new GradingResult(
            pass: true,
            score: 1.0,
            reason: '',
            namedScores: [],
            tokensUsed: [],
            componentResults: [
                new ComponentResult(
                    pass: true,
                    score: 1.0,
                    reason: 'Passed',
                    assertion: new Assertion('contains', 'test')
                ),
            ]
        ),
        id: 'id2',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test2', 'test2'),
        promptId: 'pid2',
        promptIdx: 0,
        provider: new Provider('provider2', ''),
        response: new Response('test output 2', [], false, 50, 'stop', 0.05, []),
        score: 1.0,
        success: true,
        testCase: new TestCase([], [], [], []),
        testIdx: 0,
        vars: [],
        metadata: [],
        failureReason: null
    );

    $evaluationResult = new EvaluationResult([$result1, $result2]);

    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('handleEvaluationResult');

    // Should not throw for passing results
    $method->invoke(null, $evaluationResult);

    expect(true)->toBeTrue(); // Just verify we got here without exception
});

test('assertResult throws when error is present and no grading result', function () {
    $result = new Result(
        cost: 0.1,
        error: 'Test error',
        gradingResult: null,
        id: 'id1',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test', 'test'),
        promptId: 'pid1',
        promptIdx: 0,
        provider: new Provider('provider1', ''),
        response: null,
        score: 0.0,
        success: false,
        testCase: new TestCase([], [], [], []),
        testIdx: 0,
        vars: [],
        metadata: [],
        failureReason: null
    );

    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('assertResult');

    expect(fn () => $method->invoke(null, $result))
        ->toThrow(\InvalidArgumentException::class)
        ->and(fn () => $method->invoke(null, $result))
        ->toThrow('Test error');
});

test('assertResult throws when no grading result is given', function () {
    $result = new Result(
        cost: 0.1,
        error: null,
        gradingResult: null,
        id: 'id1',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test', 'test'),
        promptId: 'pid1',
        promptIdx: 0,
        provider: new Provider('provider1', ''),
        response: new Response('test output', [], false, 50, 'stop', 0.05, []),
        score: 0.0,
        success: false,
        testCase: new TestCase([], [], [], []),
        testIdx: 0,
        vars: [],
        metadata: [],
        failureReason: null
    );

    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('assertResult');

    expect(fn () => $method->invoke(null, $result))
        ->toThrow(\InvalidArgumentException::class)
        ->and(fn () => $method->invoke(null, $result))
        ->toThrow('No grading result given');
});

test('assertComponentResult throws when component fails', function () {
    $componentResult = new ComponentResult(
        pass: false,
        score: 0.0,
        reason: 'Assertion failed',
        assertion: new Assertion('contains', 'expected')
    );

    $result = new Result(
        cost: 0.1,
        error: null,
        gradingResult: new GradingResult(
            pass: false,
            score: 0.0,
            reason: '',
            namedScores: [],
            tokensUsed: [],
            componentResults: [$componentResult]
        ),
        id: 'id1',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test prompt', 'test label'),
        promptId: 'pid1',
        promptIdx: 0,
        provider: new Provider('provider1', 'Provider 1'),
        response: new Response('actual output', [], false, 50, 'stop', 0.05, []),
        score: 0.0,
        success: false,
        testCase: new TestCase(['var' => 'value'], [], [], []),
        testIdx: 0,
        vars: ['var' => 'value'],
        metadata: [],
        failureReason: null
    );

    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('assertComponentResult');

    expect(fn () => $method->invoke(null, $componentResult, $result))
        ->toThrow(Exception::class);
});

test('buildFailureMessage creates comprehensive error message', function () {
    $componentResult = new ComponentResult(
        pass: false,
        score: 0.0,
        reason: 'Assertion failed reason',
        assertion: new Assertion('contains', 'expected value')
    );

    $result = new Result(
        cost: 0.1,
        error: null,
        gradingResult: new GradingResult(
            pass: false,
            score: 0.0,
            reason: '',
            namedScores: [],
            tokensUsed: [],
            componentResults: [$componentResult]
        ),
        id: 'id1',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test prompt text', 'test label'),
        promptId: 'pid1',
        promptIdx: 0,
        provider: new Provider('provider1', 'Provider 1'),
        response: new Response('actual output text', [], false, 50, 'stop', 0.05, []),
        score: 0.0,
        success: false,
        testCase: new TestCase(['var' => 'value'], [], [], []),
        testIdx: 0,
        vars: ['var' => 'value'],
        metadata: [],
        failureReason: null
    );

    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('buildFailureMessage');

    $message = $method->invoke(null, $componentResult, $result);

    expect($message)->toContain('Assertion failed')
        ->and($message)->toContain('provider1')
        ->and($message)->toContain('test prompt text')
        ->and($message)->toContain('actual output text')
        ->and($message)->toContain('Assertion failed reason')
        ->and($message)->toContain('contains')
        ->and($message)->toContain('expected value');
});

test('buildFailureMessage handles missing response output', function () {
    $componentResult = new ComponentResult(
        pass: false,
        score: 0.0,
        reason: 'Assertion failed',
        assertion: new Assertion('contains', 'test')
    );

    $result = new Result(
        cost: 0.1,
        error: null,
        gradingResult: new GradingResult(
            pass: false,
            score: 0.0,
            reason: '',
            namedScores: [],
            tokensUsed: [],
            componentResults: [$componentResult]
        ),
        id: 'id1',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test', 'test'),
        promptId: 'pid1',
        promptIdx: 0,
        provider: new Provider('provider1', ''),
        response: null,
        score: 0.0,
        success: false,
        testCase: new TestCase([], [], [], []),
        testIdx: 0,
        vars: [],
        metadata: [],
        failureReason: null
    );

    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('buildFailureMessage');

    $message = $method->invoke(null, $componentResult, $result);

    expect($message)->toContain('(no response available)');
});

test('buildFailureMessage handles array output', function () {
    $componentResult = new ComponentResult(
        pass: false,
        score: 0.0,
        reason: 'Assertion failed',
        assertion: new Assertion('contains', 'test')
    );

    $arrayOutput = ['name' => 'John', 'age' => 30, 'data' => ['nested' => 'value']];

    $result = new Result(
        cost: 0.1,
        error: null,
        gradingResult: new GradingResult(
            pass: false,
            score: 0.0,
            reason: '',
            namedScores: [],
            tokensUsed: [],
            componentResults: [$componentResult]
        ),
        id: 'id1',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test', 'test'),
        promptId: 'pid1',
        promptIdx: 0,
        provider: new Provider('provider1', ''),
        response: new Response($arrayOutput, [], false, 50, 'stop', 0.05, []),
        score: 0.0,
        success: false,
        testCase: new TestCase([], [], [], []),
        testIdx: 0,
        vars: [],
        metadata: [],
        failureReason: null
    );

    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('buildFailureMessage');

    $message = $method->invoke(null, $componentResult, $result);

    // Array output should be JSON encoded
    expect($message)->toContain('John')
        ->and($message)->toContain('30')
        ->and($message)->toContain('nested')
        ->and($message)->toContain('value');
});

test('encodeOutput converts array to JSON string', function () {
    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('encodeOutput');
    $method->setAccessible(true);

    $array = ['name' => 'John', 'age' => 30, 'nested' => ['key' => 'value']];
    $result = $method->invoke(null, $array);

    expect($result)->toBeString()
        ->and($result)->toContain('John')
        ->and($result)->toContain('30')
        ->and($result)->toContain('nested')
        ->and($result)->toContain('value');

    // Verify it's valid JSON
    $decoded = json_decode($result, true);
    expect($decoded)->toBe($array);
});

test('encodeOutput returns string as-is', function () {
    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('encodeOutput');
    $method->setAccessible(true);

    $string = 'This is a test output string';
    $result = $method->invoke(null, $string);

    expect($result)->toBeString()
        ->and($result)->toBe($string);
});

test('findSourceLocation maps duplicate assertions to correct source locations via ID', function () {
    // Arrange: two tracked assertions with same type/value but different IDs/locations
    $location1 = new KevinPijning\Prompt\Helpers\SourceLocation('/path/to/Test.php', 10);
    $location2 = new KevinPijning\Prompt\Helpers\SourceLocation('/path/to/Test.php', 20);

    $assertion1 = new Assertion(
        type: 'contains',
        value: 'hello',
        config: [Assertion::INTERNAL_CONFIG_KEY => [Assertion::INTERNAL_ASSERTION_ID_KEY => 'id-1']],
    );
    $assertion2 = new Assertion(
        type: 'contains',
        value: 'hello', // Same type/value!
        config: [Assertion::INTERNAL_CONFIG_KEY => [Assertion::INTERNAL_ASSERTION_ID_KEY => 'id-2']],
    );

    $tracked1 = new KevinPijning\Prompt\Internal\TrackedAssertion($assertion1, $location1);
    $tracked2 = new KevinPijning\Prompt\Internal\TrackedAssertion($assertion2, $location2);

    // Set up TestLifecycle with both tracked assertions via reflection
    $reflection = new ReflectionClass(TestLifecycle::class);
    $property = $reflection->getProperty('currentTrackedAssertions');
    $property->setValue(null, [$tracked1, $tracked2]);

    $method = $reflection->getMethod('findSourceLocation');

    // Act: simulate promptfoo returning a failure for the SECOND assertion (id-2)
    $returnedAssertion = new Assertion(
        type: 'contains',
        value: 'hello',
        config: [Assertion::INTERNAL_CONFIG_KEY => [Assertion::INTERNAL_ASSERTION_ID_KEY => 'id-2']],
    );

    $result = $method->invoke(null, $returnedAssertion);

    // Assert: should map to location2 (line 20), NOT location1 (line 10)
    expect($result->file)->toBe('/path/to/Test.php')
        ->and($result->line)->toBe(20);

    // Cleanup
    $property->setValue(null, []);
});

test('findSourceLocation falls back to type+value when no ID present', function () {
    // Arrange: tracked assertion without ID
    $location = new KevinPijning\Prompt\Helpers\SourceLocation('/path/to/Test.php', 15);
    $assertion = new Assertion(type: 'contains', value: 'test');
    $tracked = new KevinPijning\Prompt\Internal\TrackedAssertion($assertion, $location);

    $reflection = new ReflectionClass(TestLifecycle::class);
    $property = $reflection->getProperty('currentTrackedAssertions');
    $property->setValue(null, [$tracked]);

    $method = $reflection->getMethod('findSourceLocation');

    // Act: returned assertion without ID
    $returnedAssertion = new Assertion(type: 'contains', value: 'test');
    $result = $method->invoke(null, $returnedAssertion);

    // Assert: should match by type+value
    expect($result->file)->toBe('/path/to/Test.php')
        ->and($result->line)->toBe(15);

    // Cleanup
    $property->setValue(null, []);
});

test('findSourceLocation returns fallback when no match found', function () {
    $reflection = new ReflectionClass(TestLifecycle::class);
    $property = $reflection->getProperty('currentTrackedAssertions');
    $property->setValue(null, []);

    $method = $reflection->getMethod('findSourceLocation');

    $returnedAssertion = new Assertion(type: 'contains', value: 'nonexistent');
    $result = $method->invoke(null, $returnedAssertion);

    // Assert: should return fallback location (TestLifecycle.php)
    expect($result->file)->toContain('TestLifecycle.php');

    // Cleanup
    $property->setValue(null, []);
});

test('collectTrackedAssertions includes default test case assertions', function () {
    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('collectTrackedAssertions');

    // Create tracked assertions for default test case
    $defaultAssertion = new Assertion(type: 'llm-rubric', value: 'default assertion');
    $defaultTracked = new KevinPijning\Prompt\Internal\TrackedAssertion(
        $defaultAssertion,
        new KevinPijning\Prompt\Helpers\SourceLocation('/path/to/Test.php', 5)
    );

    // Create tracked assertions for regular test case
    $regularAssertion = new Assertion(type: 'contains', value: 'regular assertion');
    $regularTracked = new KevinPijning\Prompt\Internal\TrackedAssertion(
        $regularAssertion,
        new KevinPijning\Prompt\Helpers\SourceLocation('/path/to/Test.php', 10)
    );

    // Build test cases
    $defaultTestCase = new KevinPijning\Prompt\Internal\BuiltTestCase(
        variables: ['default' => 'value'],
        trackedAssertions: [$defaultTracked]
    );

    $regularTestCase = new KevinPijning\Prompt\Internal\BuiltTestCase(
        variables: ['key' => 'value'],
        trackedAssertions: [$regularTracked]
    );

    // Build evaluation with both default and regular test cases
    $builtEvaluation = new KevinPijning\Prompt\Internal\BuiltEvaluation(
        description: 'Test',
        prompts: ['test prompt'],
        providers: [],
        testCases: [$regularTestCase],
        defaultTestCase: $defaultTestCase
    );

    $result = $method->invoke(null, $builtEvaluation);

    // Should include both default and regular assertions
    expect($result)->toHaveCount(2)
        ->and($result[0]->assertion->type)->toBe('llm-rubric')
        ->and($result[0]->assertion->value)->toBe('default assertion')
        ->and($result[1]->assertion->type)->toBe('contains')
        ->and($result[1]->assertion->value)->toBe('regular assertion');
});

test('collectTrackedAssertions works without default test case', function () {
    $reflection = new ReflectionClass(TestLifecycle::class);
    $method = $reflection->getMethod('collectTrackedAssertions');

    $regularAssertion = new Assertion(type: 'contains', value: 'test');
    $regularTracked = new KevinPijning\Prompt\Internal\TrackedAssertion(
        $regularAssertion,
        new KevinPijning\Prompt\Helpers\SourceLocation('/path/to/Test.php', 10)
    );

    $regularTestCase = new KevinPijning\Prompt\Internal\BuiltTestCase(
        variables: ['key' => 'value'],
        trackedAssertions: [$regularTracked]
    );

    $builtEvaluation = new KevinPijning\Prompt\Internal\BuiltEvaluation(
        description: 'Test',
        prompts: ['test prompt'],
        providers: [],
        testCases: [$regularTestCase],
        defaultTestCase: null
    );

    $result = $method->invoke(null, $builtEvaluation);

    expect($result)->toHaveCount(1)
        ->and($result[0]->assertion->type)->toBe('contains');
});
