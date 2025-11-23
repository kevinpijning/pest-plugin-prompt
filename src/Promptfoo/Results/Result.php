<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo\Results;

class Result
{
    /**
     * @param  array<string, mixed>  $namedScores
     * @param  array<string, mixed>  $vars
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly float $cost,
        public readonly ?string $error,
        public readonly ?GradingResult $gradingResult,
        public readonly string $id,
        public readonly int $latencyMs,
        public readonly array $namedScores,
        public readonly Prompt $prompt,
        public readonly string $promptId,
        public readonly int $promptIdx,
        public readonly Provider $provider,
        public readonly ?Response $response,
        public readonly float $score,
        public readonly bool $success,
        public readonly TestCase $testCase,
        public readonly int $testIdx,
        public readonly array $vars,
        public readonly array $metadata,
        public readonly ?int $failureReason,
    ) {}
}
