<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\EvaluationResult;
use KevinPijning\Prompt\Internal\Results\ComponentResult;
use KevinPijning\Prompt\Internal\Results\GradingResult;
use KevinPijning\Prompt\Internal\Results\Prompt;
use KevinPijning\Prompt\Internal\Results\Provider;
use KevinPijning\Prompt\Internal\Results\Response;
use KevinPijning\Prompt\Internal\Results\Result;
use KevinPijning\Prompt\Internal\Results\TestCase;
use KevinPijning\Prompt\Internal\TestContext;
use KevinPijning\Prompt\Internal\TestLifecycle;

beforeEach(function () {
    TestContext::clear();
});

test('evaluate skips evaluations with empty test cases', function () {
    $evaluation = new Evaluation(['test prompt']);
    // No test cases added

    TestContext::addEvaluation($evaluation);

    // Should not throw and should clear context
    TestLifecycle::evaluate();

    expect(TestContext::getCurrentEvaluations())->toBeEmpty();
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
