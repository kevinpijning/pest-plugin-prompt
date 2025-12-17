<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanEqual
{
    /**
     * Assert that the output matches the expected value exactly.
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#equality
     */
    public function toEqual(mixed $value): self
    {
        return $this->assert(new Assertion(
            type: 'equals',
            value: $value,
        ));
    }

    /**
     * Assert that the output matches the expected value exactly.
     * Alias for toEqual().
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#equality
     */
    public function toBe(mixed $value): self
    {
        return $this->toEqual($value);
    }
}
