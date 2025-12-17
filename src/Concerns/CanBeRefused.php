<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeRefused
{
    /**
     * Assert that the model refused to perform the requested task.
     * Detects common refusal patterns like "I cannot assist with that", content filter blocks, etc.
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#is-refusal
     */
    public function toBeRefused(): self
    {
        return $this->assert(new Assertion(
            type: 'is-refusal',
        ));
    }
}
