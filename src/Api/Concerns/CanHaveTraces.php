<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;

trait CanHaveTraces
{
    /**
     * @param  array<string,mixed>  $patterns
     * @param  array<string,mixed>  $options
     */
    public function toHaveTraceSpanCount(array $patterns, ?int $min = null, ?int $max = null, array $options = []): self
    {
        $assertionOptions = array_merge($options, ['patterns' => $patterns]);
        if ($min !== null) {
            $assertionOptions['min'] = $min;
        }
        if ($max !== null) {
            $assertionOptions['max'] = $max;
        }

        return $this->assert(new Assertion(
            type: 'trace-span-count',
            value: $patterns,
            threshold: null,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>  $patterns
     * @param  array<string,mixed>  $options
     */
    public function toHaveTraceSpanDuration(array $patterns, ?float $percentile = null, ?float $maxDuration = null, array $options = []): self
    {
        $assertionOptions = array_merge($options, ['patterns' => $patterns]);
        if ($percentile !== null) {
            $assertionOptions['percentile'] = $percentile;
        }
        if ($maxDuration !== null) {
            $assertionOptions['maxDuration'] = $maxDuration;
        }

        return $this->assert(new Assertion(
            type: 'trace-span-duration',
            value: $patterns,
            threshold: null,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toHaveTraceErrorSpans(array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'trace-error-spans',
            value: null,
            threshold: null,
            options: $options,
        ));
    }
}
