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
        public ?string $id,
        public ?string $label,
        public ?float $temperature,
        public ?int $maxTokens,
        public ?float $topP,
        public ?float $frequencyPenalty,
        public ?float $presencePenalty,
        public ?array $stop,
        public array $config,
    ) {}
}
