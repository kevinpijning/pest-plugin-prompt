<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal\Results;

/**
 * @internal
 */
final readonly class Result
{
    /**
     * @param  array<string, mixed>  $namedScores
     * @param  array<string, mixed>  $vars
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public float $cost,
        public ?string $error,
        public ?GradingResult $gradingResult,
        public string $id,
        public int $latencyMs,
        public array $namedScores,
        public Prompt $prompt,
        public string $promptId,
        public int $promptIdx,
        public Provider $provider,
        public ?Response $response,
        public float $score,
        public bool $success,
        public TestCase $testCase,
        public int $testIdx,
        public array $vars,
        public array $metadata,
        public ?int $failureReason,
    ) {}
}
