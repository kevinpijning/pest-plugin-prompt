<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo\Results;

use Pest\Prompt\Api\Assertion;

class ComponentResult
{
    public function __construct(
        public readonly bool $pass,
        public readonly float $score,
        public readonly string $reason,
        public readonly Assertion $assertion,
    ) {}
}
