<?php

declare(strict_types=1);

use Pest\Prompt\Promptfoo\EvaluationResult;
use Pest\Prompt\Promptfoo\EvaluationResultBuilder;

test('build handles empty results array', function () {
    $data = [
        'results' => [
            'results' => [],
        ],
    ];

    $builder = new EvaluationResultBuilder($data);
    $result = $builder->build();

    expect($result)->toBeInstanceOf(EvaluationResult::class)
        ->and($result->results)->toBeArray()
        ->and($result->results)->toBeEmpty();
});

test('build handles result with empty component results', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => 0.1,
                    'gradingResult' => [
                        'pass' => true,
                        'score' => 1.0,
                        'reason' => 'No component results',
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

    expect($result->results[0]->gradingResult->componentResults)->toBeArray()
        ->and($result->results[0]->gradingResult->componentResults)->toBeEmpty();
});

test('build handles numeric string values correctly', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => '0.1',
                    'gradingResult' => [
                        'pass' => true,
                        'score' => '1.0',
                        'reason' => '',
                        'namedScores' => [],
                        'tokensUsed' => [],
                        'componentResults' => [],
                    ],
                    'id' => 'test-id',
                    'latencyMs' => '100',
                    'prompt' => [
                        'raw' => 'test',
                        'label' => 'test',
                    ],
                    'promptId' => 'id',
                    'promptIdx' => '0',
                    'provider' => [
                        'id' => 'provider',
                    ],
                    'response' => [
                        'output' => 'output',
                    ],
                    'score' => '1.0',
                    'success' => true,
                    'testCase' => [
                        'vars' => [],
                        'assert' => [],
                        'options' => [],
                        'metadata' => [],
                    ],
                    'testIdx' => '0',
                ],
            ],
        ],
    ];

    $builder = new EvaluationResultBuilder($data);
    $result = $builder->build();

    expect($result->results[0]->cost)->toBe(0.1)
        ->and($result->results[0]->score)->toBe(1.0)
        ->and($result->results[0]->latencyMs)->toBe(100)
        ->and($result->results[0]->promptIdx)->toBe(0)
        ->and($result->results[0]->testIdx)->toBe(0)
        ->and($result->results[0]->gradingResult->score)->toBe(1.0);
});

test('build handles boolean string values correctly', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => 0.1,
                    'gradingResult' => [
                        'pass' => 'true',
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
                        'cached' => 'false',
                    ],
                    'score' => 1.0,
                    'success' => 'true',
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

    expect($result->results[0]->gradingResult->pass)->toBeTrue()
        ->and($result->results[0]->success)->toBeTrue()
        ->and($result->results[0]->response->cached)->toBeFalse();
});

test('build throws error when results.results key is missing', function () {
    $data = [
        'results' => [
            // 'results' key is missing
        ],
    ];

    $builder = new EvaluationResultBuilder($data);

    expect(fn () => $builder->build())
        ->toThrow(\InvalidArgumentException::class)
        ->and(fn () => $builder->build())
        ->toThrow('Missing required key "results.results"');
});

test('build throws error when results key is missing', function () {
    $data = [
        // 'results' key is completely missing
    ];

    $builder = new EvaluationResultBuilder($data);

    expect(fn () => $builder->build())
        ->toThrow(\InvalidArgumentException::class)
        ->and(fn () => $builder->build())
        ->toThrow('Missing required key "results"');
});

test('build throws error when results.results is null', function () {
    $data = [
        'results' => [
            'results' => null,
        ],
    ];

    $builder = new EvaluationResultBuilder($data);

    expect(fn () => $builder->build())
        ->toThrow(\InvalidArgumentException::class)
        ->and(fn () => $builder->build())
        ->toThrow('Key "results.results" cannot be null');
});
