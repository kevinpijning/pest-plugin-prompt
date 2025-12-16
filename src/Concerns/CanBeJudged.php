<?php

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeJudged
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toBeJudged(string $contains, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'llm-rubric',
            value: $contains,
            threshold: $threshold,
            options: $options,
        ));
    }
}
