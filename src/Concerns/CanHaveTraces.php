<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanHaveTraces
{
    /**
     * Assert span count matching patterns with min/max thresholds.
     *
     * @param  array<string,mixed>  $patterns  Patterns to match spans
     * @param  int|null  $min  Minimum number of spans
     * @param  int|null  $max  Maximum number of spans
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#trace-span-count
     */
    public function toHaveTraceSpanCount(array $patterns, ?int $min = null, ?int $max = null): self
    {
        $value = ['patterns' => $patterns];
        if ($min !== null) {
            $value['min'] = $min;
        }
        if ($max !== null) {
            $value['max'] = $max;
        }

        return $this->assert(new Assertion(
            type: 'trace-span-count',
            value: $value,
        ));
    }

    /**
     * Assert span durations with percentile support.
     *
     * @param  array<string,mixed>  $patterns  Patterns to match spans
     * @param  float|null  $percentile  Percentile for duration calculation
     * @param  float|null  $maxDuration  Maximum duration in milliseconds
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#trace-span-duration
     */
    public function toHaveTraceSpanDuration(array $patterns, ?float $percentile = null, ?float $maxDuration = null): self
    {
        $value = ['patterns' => $patterns];
        if ($percentile !== null) {
            $value['percentile'] = $percentile;
        }
        if ($maxDuration !== null) {
            $value['maxDuration'] = $maxDuration;
        }

        return $this->assert(new Assertion(
            type: 'trace-span-duration',
            value: $value,
        ));
    }

    /**
     * Detect errors in traces by status codes, attributes, and messages.
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#trace-error-spans
     */
    public function toHaveTraceErrorSpans(): self
    {
        return $this->assert(new Assertion(
            type: 'trace-error-spans',
        ));
    }
}
