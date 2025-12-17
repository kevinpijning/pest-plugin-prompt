<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanMatch
{
    /**
     * Assert that the output starts with the specified prefix.
     * Note: promptfoo's starts-with is case-sensitive.
     *
     * @param  string  $prefix  The prefix to check for
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#starts-with
     */
    public function startsWith(string $prefix): self
    {
        return $this->assert(new Assertion(
            type: 'starts-with',
            value: $prefix,
        ));
    }

    /**
     * Assert that the output matches the provided regular expression pattern.
     *
     * @param  string  $pattern  The regex pattern to match against
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#regex
     */
    public function toMatchRegex(string $pattern): self
    {
        return $this->assert(new Assertion(
            type: 'regex',
            value: $pattern,
        ));
    }
}
