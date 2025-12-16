<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal\Results;

/**
 * @internal
 */
final readonly class Response
{
    /**
     * @param  string|array<string, mixed>  $output
     * @param  array<string, mixed>  $tokenUsage
     * @param  array<string, mixed>  $guardrails
     */
    public function __construct(
        public string|array $output,
        public array $tokenUsage,
        public bool $cached,
        public int $latencyMs,
        public string $finishReason,
        public float $cost,
        public array $guardrails,
    ) {}
}
