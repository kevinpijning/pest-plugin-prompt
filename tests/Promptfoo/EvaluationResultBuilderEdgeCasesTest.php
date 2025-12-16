<?php

declare(strict_types=1);

use KevinPijning\Prompt\Internal\EvaluationResult;
use KevinPijning\Prompt\Promptfoo\EvaluationResultBuilder;

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

test('build throws error when results.results is not an array', function () {
    $data = [
        'results' => [
            'results' => 'not-an-array',
        ],
    ];

    $builder = new EvaluationResultBuilder($data);

    expect(fn () => $builder->build())
        ->toThrow(\InvalidArgumentException::class)
        ->and(fn () => $builder->build())
        ->toThrow('Key "results.results" must be an array, got: string');
});

test('buildResponse returns null when response has error', function () {
    $data = [
        'results' => [
            'results' => [
                [
                    'cost' => 0.1,
                    'gradingResult' => null,
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
                        'error' => 'Some error occurred',
                    ],
                    'score' => 0.0,
                    'success' => false,
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

    expect($result->results[0]->response)->toBeNull();
});

test('fromJson throws error when file cannot be read', function () {
    $nonExistentFile = '/tmp/non-existent-file-'.uniqid().'.json';

    // Suppress warnings for this test since we're intentionally testing an error case
    set_error_handler(function ($errno, $errstr) {
        // Suppress file_get_contents warnings
        if (str_contains($errstr, 'file_get_contents') && str_contains($errstr, 'Failed to open stream')) {
            return true; // Suppress this warning
        }

        return false; // Let other errors through
    }, E_WARNING);

    try {
        expect(fn () => EvaluationResultBuilder::fromJson($nonExistentFile))
            ->toThrow(\InvalidArgumentException::class)
            ->and(fn () => EvaluationResultBuilder::fromJson($nonExistentFile))
            ->toThrow("Failed to read file: {$nonExistentFile}");
    } finally {
        // Always restore the error handler
        restore_error_handler();
    }
});
