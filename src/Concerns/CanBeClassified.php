<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeClassified
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toBeClassified(string $provider, string $expectedClass, ?float $threshold = null, array $options = []): self
    {
        $assertionOptions = array_merge($options, ['provider' => $provider]);

        return $this->assert(new Assertion(
            type: 'classifier',
            value: $expectedClass,
            threshold: $threshold,
            options: $assertionOptions,
        ));
    }
}
