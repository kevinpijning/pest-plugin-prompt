<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeClassified
{
    /**
     * Assert that the output is classified as the expected class by a HuggingFace classifier.
     * Useful for sentiment analysis, toxicity detection, bias detection, PII detection, etc.
     *
     * @param  string  $provider  HuggingFace classifier (e.g., "huggingface:text-classification:facebook/roberta-hate-speech-dynabench-r4-target")
     * @param  string  $expectedClass  The expected class name (e.g., "nothate", "SAFE")
     * @param  float|null  $threshold  Minimum confidence score (0.0 to 1.0)
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#classifier
     */
    public function toBeClassified(string $provider, string $expectedClass, ?float $threshold = null): self
    {
        return $this->assert(new Assertion(
            type: 'classifier',
            value: $expectedClass,
            threshold: $threshold,
            provider: $provider,
        ));
    }
}
