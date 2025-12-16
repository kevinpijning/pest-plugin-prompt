<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanEqual
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toEqual(mixed $value, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'equals',
            value: $value,
            threshold: $threshold,
            options: $options,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toBe(mixed $value, ?float $threshold = null, array $options = []): self
    {
        return $this->toEqual($value, $threshold, $options);
    }
}
