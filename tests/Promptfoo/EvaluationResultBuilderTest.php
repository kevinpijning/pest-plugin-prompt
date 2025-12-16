<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Internal\EvaluationResult;
use KevinPijning\Prompt\Internal\Results\ComponentResult;
use KevinPijning\Prompt\Internal\Results\GradingResult;
use KevinPijning\Prompt\Internal\Results\Prompt;
use KevinPijning\Prompt\Internal\Results\Provider;
use KevinPijning\Prompt\Internal\Results\Response;
use KevinPijning\Prompt\Internal\Results\Result;
use KevinPijning\Prompt\Internal\Results\TestCase;
use KevinPijning\Prompt\Promptfoo\EvaluationResultBuilder;

test('fromJson creates EvaluationResult from JSON file', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');

    expect($result)->toBeInstanceOf(EvaluationResult::class)
        ->and($result->results)->toBeArray()
        ->and($result->results)->toHaveCount(2);
});

test('fromJson creates Result objects with correct structure', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');
    $firstResult = $result->results[0];

    expect($firstResult)->toBeInstanceOf(Result::class)
        ->and($firstResult->cost)->toBe(0.00000975)
        ->and($firstResult->error)->toBe('Expected output to contain "muda"')
        ->and($firstResult->id)->toBe('31ce3910-d3c3-418e-9de7-e50e1e2f54bc')
        ->and($firstResult->latencyMs)->toBe(8)
        ->and($firstResult->promptId)->toBe('c66eeb08d0abd527a917d6422bee292409d7d6a3750e1ab3bc80a386cd9971bd')
        ->and($firstResult->promptIdx)->toBe(0)
        ->and($firstResult->score)->toBe(0.5)
        ->and($firstResult->success)->toBeFalse()
        ->and($firstResult->testIdx)->toBe(0)
        ->and($firstResult->failureReason)->toBe(1);
});

test('fromJson creates Prompt objects correctly', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');
    $prompt = $result->results[0]->prompt;

    expect($prompt)->toBeInstanceOf(Prompt::class)
        ->and($prompt->raw)->toBe('translate Hello World! to es')
        ->and($prompt->label)->toBe('translate {{message}} to {{language}}');
});

test('fromJson creates Provider objects correctly', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');
    $provider = $result->results[0]->provider;

    expect($provider)->toBeInstanceOf(Provider::class)
        ->and($provider->id)->toBe('openai:gpt-4o-mini')
        ->and($provider->label)->toBe('');
});

test('fromJson creates Response objects correctly', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');
    $response = $result->results[0]->response;

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->output)->toBe('"Hello World!" in Spanish is "¡Hola, Mundo!"')
        ->and($response->cached)->toBeTrue()
        ->and($response->latencyMs)->toBe(959)
        ->and($response->finishReason)->toBe('stop')
        ->and($response->cost)->toBe(0.00000975)
        ->and($response->tokenUsage)->toBeArray()
        ->and($response->tokenUsage['cached'])->toBe(26)
        ->and($response->tokenUsage['total'])->toBe(26)
        ->and($response->guardrails)->toBeArray()
        ->and($response->guardrails['flagged'])->toBeFalse();
});

test('fromJson creates TestCase objects correctly', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');
    $testCase = $result->results[0]->testCase;

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->vars)->toBeArray()
        ->and($testCase->vars['message'])->toBe('Hello World!')
        ->and($testCase->vars['language'])->toBe('es')
        ->and($testCase->assert)->toBeArray()
        ->and($testCase->assert)->toHaveCount(2)
        ->and($testCase->options)->toBeArray()
        ->and($testCase->metadata)->toBeArray();
});

test('fromJson creates GradingResult objects correctly', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');
    $gradingResult = $result->results[0]->gradingResult;

    expect($gradingResult)->toBeInstanceOf(GradingResult::class)
        ->and($gradingResult->pass)->toBeFalse()
        ->and($gradingResult->score)->toBe(0.5)
        ->and($gradingResult->reason)->toBe('Expected output to contain "muda"')
        ->and($gradingResult->namedScores)->toBeArray()
        ->and($gradingResult->tokensUsed)->toBeArray()
        ->and($gradingResult->componentResults)->toBeArray()
        ->and($gradingResult->componentResults)->toHaveCount(2);
});

test('fromJson creates ComponentResult objects correctly', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');
    $componentResults = $result->results[0]->gradingResult->componentResults;

    expect($componentResults[0])->toBeInstanceOf(ComponentResult::class)
        ->and($componentResults[0]->pass)->toBeTrue()
        ->and($componentResults[0]->score)->toBe(1.0)
        ->and($componentResults[0]->reason)->toBe('Assertion passed')
        ->and($componentResults[0]->assertion)->toBeInstanceOf(Assertion::class)
        ->and($componentResults[0]->assertion->type)->toBe('icontains')
        ->and($componentResults[0]->assertion->value)->toBe('Hola');

    expect($componentResults[1])->toBeInstanceOf(ComponentResult::class)
        ->and($componentResults[1]->pass)->toBeFalse()
        ->and($componentResults[1]->score)->toBe(0.0)
        ->and($componentResults[1]->reason)->toBe('Expected output to contain "muda"')
        ->and($componentResults[1]->assertion)->toBeInstanceOf(Assertion::class)
        ->and($componentResults[1]->assertion->type)->toBe('icontains')
        ->and($componentResults[1]->assertion->value)->toBe('muda');
});

test('fromJson handles optional fields correctly', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');
    $firstResult = $result->results[0];

    expect($firstResult->namedScores)->toBeArray()
        ->and($firstResult->vars)->toBeArray()
        ->and($firstResult->vars['message'])->toBe('Hello World!')
        ->and($firstResult->metadata)->toBeArray();
});

test('fromJson handles multiple results correctly', function () {
    $result = EvaluationResultBuilder::fromJson(__DIR__.'/../fixtures/promptfoo_output.json');

    expect($result->results)->toHaveCount(2)
        ->and($result->results[0])->toBeInstanceOf(Result::class)
        ->and($result->results[1])->toBeInstanceOf(Result::class)
        ->and($result->results[0]->prompt->raw)->not->toBe($result->results[1]->prompt->raw);
});

test('build creates EvaluationResult from array data', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => 0.1,
                    'error' => null,
                    'gradingResult' => [
                        'pass' => true,
                        'score' => 1.0,
                        'reason' => 'All assertions passed',
                        'namedScores' => [],
                        'tokensUsed' => [],
                        'componentResults' => [
                            [
                                'pass' => true,
                                'score' => 1.0,
                                'reason' => 'Assertion passed',
                                'assertion' => [
                                    'type' => 'contains',
                                    'value' => 'test',
                                ],
                            ],
                        ],
                    ],
                    'id' => 'test-id',
                    'latencyMs' => 100,
                    'namedScores' => [],
                    'prompt' => [
                        'raw' => 'test prompt',
                        'label' => 'test label',
                    ],
                    'promptId' => 'prompt-id',
                    'promptIdx' => 0,
                    'provider' => [
                        'id' => 'test-provider',
                        'label' => 'Test Provider',
                    ],
                    'response' => [
                        'output' => 'test output',
                        'tokenUsage' => [],
                        'cached' => false,
                        'latencyMs' => 50,
                        'finishReason' => 'stop',
                        'cost' => 0.05,
                        'guardrails' => [],
                    ],
                    'score' => 1.0,
                    'success' => true,
                    'testCase' => [
                        'vars' => ['key' => 'value'],
                        'assert' => [],
                        'options' => [],
                        'metadata' => [],
                    ],
                    'testIdx' => 0,
                    'vars' => ['key' => 'value'],
                    'metadata' => [],
                    'failureReason' => null,
                ],
            ],
        ],
    ];

    $builder = new EvaluationResultBuilder($data);
    $result = $builder->build();

    expect($result)->toBeInstanceOf(EvaluationResult::class)
        ->and($result->results)->toHaveCount(1)
        ->and($result->results[0]->success)->toBeTrue()
        ->and($result->results[0]->error)->toBeNull()
        ->and($result->results[0]->failureReason)->toBeNull();
});

test('build handles missing optional fields gracefully', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => 0.1,
                    'gradingResult' => [
                        'pass' => true,
                        'score' => 1.0,
                        'reason' => '',
                        'namedScores' => [],
                        'tokensUsed' => [],
                        'componentResults' => [],
                    ],
                    'id' => 'test-id',
                    'latencyMs' => 100,
                    'prompt' => [
                        'raw' => 'test',
                        'label' => 'test',
                    ],
                    'promptId' => 'id',
                    'promptIdx' => 0,
                    'provider' => [
                        'id' => 'provider',
                    ],
                    'response' => [
                        'output' => 'output',
                    ],
                    'score' => 1.0,
                    'success' => true,
                    'testCase' => [
                        'vars' => [],
                        'assert' => [],
                        'options' => [],
                        'metadata' => [],
                    ],
                    'testIdx' => 0,
                ],
            ],
        ],
    ];

    $builder = new EvaluationResultBuilder($data);
    $result = $builder->build();

    expect($result->results[0])->toBeInstanceOf(Result::class)
        ->and($result->results[0]->error)->toBeNull()
        ->and($result->results[0]->namedScores)->toBeArray()
        ->and($result->results[0]->vars)->toBeArray()
        ->and($result->results[0]->metadata)->toBeArray()
        ->and($result->results[0]->failureReason)->toBeNull()
        ->and($result->results[0]->provider->label)->toBe('')
        ->and($result->results[0]->response->cached)->toBeFalse()
        ->and($result->results[0]->response->latencyMs)->toBe(0)
        ->and($result->results[0]->response->finishReason)->toBe('')
        ->and($result->results[0]->response->cost)->toBe(0.0);
});

test('build handles assertion with optional threshold and options', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => 0.1,
                    'gradingResult' => [
                        'pass' => true,
                        'score' => 1.0,
                        'reason' => '',
                        'namedScores' => [],
                        'tokensUsed' => [],
                        'componentResults' => [
                            [
                                'pass' => true,
                                'score' => 1.0,
                                'reason' => 'Passed',
                                'assertion' => [
                                    'type' => 'contains',
                                    'value' => 'test',
                                    'threshold' => 0.8,
                                    'options' => ['key' => 'value'],
                                ],
                            ],
                        ],
                    ],
                    'id' => 'test-id',
                    'latencyMs' => 100,
                    'prompt' => [
                        'raw' => 'test',
                        'label' => 'test',
                    ],
                    'promptId' => 'id',
                    'promptIdx' => 0,
                    'provider' => [
                        'id' => 'provider',
                    ],
                    'response' => [
                        'output' => 'output',
                    ],
                    'score' => 1.0,
                    'success' => true,
                    'testCase' => [
                        'vars' => [],
                        'assert' => [],
                        'options' => [],
                        'metadata' => [],
                    ],
                    'testIdx' => 0,
                ],
            ],
        ],
    ];

    $builder = new EvaluationResultBuilder($data);
    $result = $builder->build();
    $assertion = $result->results[0]->gradingResult->componentResults[0]->assertion;

    expect($assertion->threshold)->toBe(0.8)
        ->and($assertion->options)->toBe(['key' => 'value']);
});

test('build handles assertion without value key (e.g., toBeJson)', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => 0.1,
                    'gradingResult' => [
                        'pass' => true,
                        'score' => 1.0,
                        'reason' => '',
                        'namedScores' => [],
                        'tokensUsed' => [],
                        'componentResults' => [
                            [
                                'pass' => true,
                                'score' => 1.0,
                                'reason' => 'Passed',
                                'assertion' => [
                                    'type' => 'is-json',
                                    // value key is missing (filtered out when null)
                                ],
                            ],
                        ],
                    ],
                    'id' => 'test-id',
                    'latencyMs' => 100,
                    'prompt' => [
                        'raw' => 'test',
                        'label' => 'test',
                    ],
                    'promptId' => 'id',
                    'promptIdx' => 0,
                    'provider' => [
                        'id' => 'provider',
                    ],
                    'response' => [
                        'output' => 'output',
                    ],
                    'score' => 1.0,
                    'success' => true,
                    'testCase' => [
                        'vars' => [],
                        'assert' => [],
                        'options' => [],
                        'metadata' => [],
                    ],
                    'testIdx' => 0,
                ],
            ],
        ],
    ];

    $builder = new EvaluationResultBuilder($data);
    $result = $builder->build();
    $assertion = $result->results[0]->gradingResult->componentResults[0]->assertion;

    expect($assertion->type)->toBe('is-json')
        ->and($assertion->value)->toBeNull()
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBeNull();
});

test('build handles assertion without threshold and options keys', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => 0.1,
                    'gradingResult' => [
                        'pass' => true,
                        'score' => 1.0,
                        'reason' => '',
                        'namedScores' => [],
                        'tokensUsed' => [],
                        'componentResults' => [
                            [
                                'pass' => true,
                                'score' => 1.0,
                                'reason' => 'Passed',
                                'assertion' => [
                                    'type' => 'contains',
                                    'value' => 'test',
                                    // threshold and options keys are missing (filtered out when null)
                                ],
                            ],
                        ],
                    ],
                    'id' => 'test-id',
                    'latencyMs' => 100,
                    'prompt' => [
                        'raw' => 'test',
                        'label' => 'test',
                    ],
                    'promptId' => 'id',
                    'promptIdx' => 0,
                    'provider' => [
                        'id' => 'provider',
                    ],
                    'response' => [
                        'output' => 'output',
                    ],
                    'score' => 1.0,
                    'success' => true,
                    'testCase' => [
                        'vars' => [],
                        'assert' => [],
                        'options' => [],
                        'metadata' => [],
                    ],
                    'testIdx' => 0,
                ],
            ],
        ],
    ];

    $builder = new EvaluationResultBuilder($data);
    $result = $builder->build();
    $assertion = $result->results[0]->gradingResult->componentResults[0]->assertion;

    expect($assertion->type)->toBe('contains')
        ->and($assertion->value)->toBe('test')
        ->and($assertion->threshold)->toBeNull()
        ->and($assertion->options)->toBeNull();
});
