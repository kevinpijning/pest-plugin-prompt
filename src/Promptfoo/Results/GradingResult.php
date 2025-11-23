<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo\Results;

class GradingResult
{
    /**
     * @param  array<string, mixed>  $namedScores
     * @param  array<string, mixed>  $tokensUsed
     * @param  array<int, ComponentResult>  $componentResults
     */
    public function __construct(
        public readonly bool $pass,
        public readonly float $score,
        public readonly string $reason,
        public readonly array $namedScores,
        public readonly array $tokensUsed,
        public readonly array $componentResults,
    ) {}
}
