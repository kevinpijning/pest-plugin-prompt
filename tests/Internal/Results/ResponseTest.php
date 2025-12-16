<?php

declare(strict_types=1);

use KevinPijning\Prompt\Internal\Results\Response;

test('Response can be instantiated with all properties', function () {
    $response = new Response(
        output: 'test output',
        tokenUsage: ['total' => 100, 'cached' => 50],
        cached: true,
        latencyMs: 500,
        finishReason: 'stop',
        cost: 0.01,
        guardrails: ['flagged' => false],
    );

    expect($response->output)->toBe('test output')
        ->and($response->tokenUsage)->toBeArray()
        ->and($response->tokenUsage['total'])->toBe(100)
        ->and($response->tokenUsage['cached'])->toBe(50)
        ->and($response->cached)->toBeTrue()
        ->and($response->latencyMs)->toBe(500)
        ->and($response->finishReason)->toBe('stop')
        ->and($response->cost)->toBe(0.01)
        ->and($response->guardrails)->toBeArray()
        ->and($response->guardrails['flagged'])->toBeFalse();
});

test('Response can be instantiated with empty arrays', function () {
    $response = new Response(
        output: 'output',
        tokenUsage: [],
        cached: false,
        latencyMs: 0,
        finishReason: '',
        cost: 0.0,
        guardrails: [],
    );

    expect($response->tokenUsage)->toBeArray()
        ->and($response->tokenUsage)->toBeEmpty()
        ->and($response->guardrails)->toBeArray()
        ->and($response->guardrails)->toBeEmpty();
});

test('Response transforms array output to JSON string', function () {
    $response = new Response(
        output: ['message' => 'Hello', 'code' => 200],
        tokenUsage: [],
        cached: false,
        latencyMs: 100,
        finishReason: 'stop',
        cost: 0.0,
        guardrails: [],
    );

    expect($response->output)->toBeString()
        ->and($response->output)->toBe('{"message":"Hello","code":200}');
});

test('Response keeps string output as string', function () {
    $response = new Response(
        output: 'Simple text output',
        tokenUsage: [],
        cached: false,
        latencyMs: 100,
        finishReason: 'stop',
        cost: 0.0,
        guardrails: [],
    );

    expect($response->output)->toBeString()
        ->and($response->output)->toBe('Simple text output');
});
