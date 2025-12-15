<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;

trait CanHavePerformance
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toHaveCost(?float $maxCost = null, array $options = []): self
    {
        $assertionOptions = $options;
        if ($maxCost !== null) {
            $assertionOptions['maxCost'] = $maxCost;
        }

        return $this->assert(new Assertion(
            type: 'cost',
            value: $maxCost,
            threshold: $maxCost,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toHaveLatency(?int $maxMilliseconds = null, array $options = []): self
    {
        $assertionOptions = $options;
        if ($maxMilliseconds !== null) {
            $assertionOptions['maxLatency'] = $maxMilliseconds;
        }

        return $this->assert(new Assertion(
            type: 'latency',
            value: $maxMilliseconds,
            threshold: $maxMilliseconds !== null ? (float) $maxMilliseconds : null,
            options: $assertionOptions,
        ));
    }
}

