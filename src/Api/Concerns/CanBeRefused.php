<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;

trait CanBeRefused
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toBeRefused(array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'is-refusal',
            value: null,
            threshold: null,
            options: $options,
        ));
    }
}
