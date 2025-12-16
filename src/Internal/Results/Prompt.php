<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal\Results;

/**
 * @internal
 */
final readonly class Prompt
{
    public function __construct(
        public string $raw,
        public string $label,
    ) {}
}
