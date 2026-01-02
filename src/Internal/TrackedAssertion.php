<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Helpers\SourceLocation;

final readonly class TrackedAssertion
{
    public function __construct(
        public Assertion $assertion,
        public ?SourceLocation $sourceLocation = null,
    ) {}

    public function matches(Assertion $other): bool
    {
        return $this->assertion->type === $other->type
            && $this->assertion->value === $other->value;
    }
}
