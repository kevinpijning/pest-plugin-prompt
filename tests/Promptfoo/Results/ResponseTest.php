<?php

declare(strict_types=1);

use KevinPijning\Prompt\Promptfoo\Results\Response;

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
