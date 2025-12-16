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

test('Response can be instantiated with array output', function () {
    $arrayOutput = ['name' => 'John', 'age' => 30, 'email' => 'john@example.com'];

    $response = new Response(
        output: $arrayOutput,
        tokenUsage: ['total' => 100],
        cached: false,
        latencyMs: 200,
        finishReason: 'stop',
        cost: 0.02,
        guardrails: [],
    );

    expect($response->output)->toBeArray()
        ->and($response->output)->toBe($arrayOutput)
        ->and($response->output['name'])->toBe('John')
        ->and($response->output['age'])->toBe(30);
});
