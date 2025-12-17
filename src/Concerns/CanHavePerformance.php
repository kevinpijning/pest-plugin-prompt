<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanHavePerformance
{
    /**
     * Assert that the inference cost is below a threshold.
     *
     * @param  float  $maxCost  Maximum cost threshold
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#cost
     */
    public function toHaveCost(float $maxCost): self
    {
        return $this->assert(new Assertion(
            type: 'cost',
            threshold: $maxCost,
        ));
    }

    /**
     * Assert that the latency is below a threshold.
     *
     * @param  int  $maxMilliseconds  Maximum latency in milliseconds
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#latency
     */
    public function toHaveLatency(int $maxMilliseconds): self
    {
        return $this->assert(new Assertion(
            type: 'latency',
            threshold: (float) $maxMilliseconds,
        ));
    }
}
