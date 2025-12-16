<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal\Results;

/**
 * @internal
 */
final readonly class GradingResult
{
    /**
     * @param  array<string, mixed>  $namedScores
     * @param  array<string, mixed>  $tokensUsed
     * @param  array<int, ComponentResult>  $componentResults
     */
    public function __construct(
        public bool $pass,
        public float $score,
        public string $reason,
        public array $namedScores,
        public array $tokensUsed,
        public array $componentResults,
    ) {}
}
