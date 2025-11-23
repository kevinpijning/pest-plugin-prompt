<?php

declare(strict_types=1);

use Pest\Prompt\Promptfoo\Results\GradingResult;
use Pest\Prompt\Promptfoo\Results\Prompt;
use Pest\Prompt\Promptfoo\Results\Provider;
use Pest\Prompt\Promptfoo\Results\Response;
use Pest\Prompt\Promptfoo\Results\Result;
use Pest\Prompt\Promptfoo\Results\TestCase;

test('Result can be instantiated with all properties', function () {
    $gradingResult = new GradingResult(
        pass: true,
        score: 1.0,
        reason: 'Passed',
        namedScores: [],
        tokensUsed: [],
        componentResults: [],
    );

    $prompt = new Prompt('test prompt', 'test label');
    $provider = new Provider('test-provider', 'Test Provider');
    $response = new Response(
        output: 'output',
        tokenUsage: [],
        cached: false,
        latencyMs: 0,
        finishReason: 'stop',
        cost: 0.0,
        guardrails: [],
    );

    $testCase = new TestCase(
        vars: [],
        assert: [],
        options: [],
        metadata: [],
    );

    $result = new Result(
        cost: 0.1,
        error: null,
        gradingResult: $gradingResult,
        id: 'test-id',
        latencyMs: 100,
        namedScores: [],
        prompt: $prompt,
        promptId: 'prompt-id',
        promptIdx: 0,
        provider: $provider,
        response: $response,
        score: 1.0,
        success: true,
        testCase: $testCase,
        testIdx: 0,
        vars: ['key' => 'value'],
        metadata: [],
        failureReason: null,
    );

    expect($result->cost)->toBe(0.1)
        ->and($result->error)->toBeNull()
        ->and($result->gradingResult)->toBe($gradingResult)
        ->and($result->id)->toBe('test-id')
        ->and($result->latencyMs)->toBe(100)
        ->and($result->prompt)->toBe($prompt)
        ->and($result->promptId)->toBe('prompt-id')
        ->and($result->promptIdx)->toBe(0)
        ->and($result->provider)->toBe($provider)
        ->and($result->response)->toBe($response)
        ->and($result->score)->toBe(1.0)
        ->and($result->success)->toBeTrue()
        ->and($result->testCase)->toBe($testCase)
        ->and($result->testIdx)->toBe(0)
        ->and($result->vars)->toBe(['key' => 'value'])
        ->and($result->metadata)->toBeArray()
        ->and($result->failureReason)->toBeNull();
});

test('Result can be instantiated with error and failure reason', function () {
    $gradingResult = new GradingResult(
        pass: false,
        score: 0.0,
        reason: 'Failed',
        namedScores: [],
        tokensUsed: [],
        componentResults: [],
    );

    $result = new Result(
        cost: 0.1,
        error: 'Expected output to contain "test"',
        gradingResult: $gradingResult,
        id: 'test-id',
        latencyMs: 100,
        namedScores: [],
        prompt: new Prompt('test', 'test'),
        promptId: 'id',
        promptIdx: 0,
        provider: new Provider('provider', ''),
        response: new Response('output', [], false, 0, '', 0.0, []),
        score: 0.0,
        success: false,
        testCase: new TestCase([], [], [], []),
        testIdx: 0,
        vars: [],
        metadata: [],
        failureReason: 1,
    );

    expect($result->error)->toBe('Expected output to contain "test"')
        ->and($result->success)->toBeFalse()
        ->and($result->failureReason)->toBe(1);
});
