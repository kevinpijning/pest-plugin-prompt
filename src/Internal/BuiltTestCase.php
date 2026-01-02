<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\Assertion;

final readonly class BuiltTestCase
{
    /**
     * @param  array<string,mixed>  $variables
     * @param  TrackedAssertion[]  $trackedAssertions
     */
    public function __construct(
        public array $variables,
        public array $trackedAssertions,
    ) {}

    /**
     * @return Assertion[]
     */
    public function assertions(): array
    {
        return array_map(
            static fn (TrackedAssertion $tracked): Assertion => $tracked->assertion,
            $this->trackedAssertions,
        );
    }
}
