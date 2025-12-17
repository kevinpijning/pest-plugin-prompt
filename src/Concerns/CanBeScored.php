<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeScored
{
    /**
     * Assert that the output passes Pi Labs' preference scoring model evaluation.
     * Requires WITHPI_API_KEY environment variable to be set.
     *
     * @param  string  $rubric  The evaluation criteria/rubric
     * @param  float|null  $threshold  Minimum score to pass (0.0 to 1.0, default: 0.5)
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#pi
     */
    public function toBeScoredByPi(string $rubric, ?float $threshold = null): self
    {
        return $this->assert(new Assertion(
            type: 'pi',
            value: $rubric,
            threshold: $threshold,
        ));
    }
}
