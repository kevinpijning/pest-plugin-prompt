<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo\Results;

class Response
{
    /**
     * @param  array<string, mixed>  $tokenUsage
     * @param  array<string, mixed>  $guardrails
     */
    public function __construct(
        public readonly string $output,
        public readonly array $tokenUsage,
        public readonly bool $cached,
        public readonly int $latencyMs,
        public readonly string $finishReason,
        public readonly float $cost,
        public readonly array $guardrails,
    ) {}
}
