<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

/**
 * @internal
 */
final readonly class BuiltProvider
{
    /**
     * @param  string[]|null  $stop
     * @param  array<string,mixed>  $config
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $label,
        public readonly ?float $temperature,
        public readonly ?int $maxTokens,
        public readonly ?float $topP,
        public readonly ?float $frequencyPenalty,
        public readonly ?float $presencePenalty,
        public readonly ?array $stop,
        public readonly array $config,
    ) {}
}

