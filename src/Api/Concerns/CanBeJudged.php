<?php

namespace Pest\Prompt\Api\Concerns;

use Pest\Prompt\Api\Assertion;

trait CanBeJudged
{
    public function toBeJudged(string $contains, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            'llm-rubric',
            $contains,
            $threshold,
            $options,
        ));
    }
}
