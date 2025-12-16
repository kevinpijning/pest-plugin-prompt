<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal\Results;

use KevinPijning\Prompt\Assertion;

/**
 * @internal
 */
final readonly class ComponentResult
{
    public function __construct(
        public bool $pass,
        public float $score,
        public string $reason,
        public Assertion $assertion,
    ) {}
}
