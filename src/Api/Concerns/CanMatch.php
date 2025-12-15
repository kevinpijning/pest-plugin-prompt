<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;

trait CanMatch
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function startsWith(string $prefix, bool $strict = false, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'starts-with',
            value: $prefix,
            threshold: $threshold,
            options: array_merge($options, ['caseSensitive' => $strict]),
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toMatchRegex(string $pattern, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'regex',
            value: $pattern,
            threshold: $threshold,
            options: $options,
        ));
    }
}
