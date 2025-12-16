<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeScored
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toBeScoredByPi(string $rubric, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'pi',
            value: $rubric,
            threshold: $threshold,
            options: $options,
        ));
    }
}
