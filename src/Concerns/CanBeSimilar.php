<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeSimilar
{
    /**
     * Assert that the output is semantically similar to the expected value using embedding similarity.
     *
     * @param  string|string[]  $expected  The expected text or array of expected texts
     * @param  float|null  $threshold  Minimum similarity score (0.0 to 1.0, default: 0.75)
     * @param  string|null  $provider  Embedding provider (e.g., "huggingface:sentence-similarity:sentence-transformers/all-MiniLM-L6-v2")
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#similar
     */
    public function toBeSimilar(string|array $expected, ?float $threshold = null, ?string $provider = null): self
    {
        return $this->assert(new Assertion(
            type: 'similar',
            value: $expected,
            threshold: $threshold,
            provider: $provider,
        ));
    }

    /**
     * Assert that the Levenshtein distance between output and expected is below a threshold.
     *
     * @param  string  $expected  The expected text
     * @param  float|null  $threshold  Maximum Levenshtein distance
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#levenshtein-distance
     */
    public function toHaveLevenshtein(string $expected, ?float $threshold = null): self
    {
        return $this->assert(new Assertion(
            type: 'levenshtein',
            value: $expected,
            threshold: $threshold,
        ));
    }

    /**
     * Assert that the Rouge-N score is above a threshold.
     *
     * @param  int  $n  The n-gram size (e.g., 1 for unigrams, 2 for bigrams)
     * @param  string|string[]  $expected  The reference text(s)
     * @param  float|null  $threshold  Minimum Rouge-N score (0.0 to 1.0)
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#rouge-n
     */
    public function toHaveRougeN(int $n, string|array $expected, ?float $threshold = null): self
    {
        return $this->assert(new Assertion(
            type: 'rouge-n',
            value: $expected,
            threshold: $threshold,
            config: ['n' => $n],
        ));
    }

    /**
     * Assert that the F-score is above a threshold.
     *
     * @param  string|string[]  $expected  The reference text(s)
     * @param  float|null  $threshold  Minimum F-score (0.0 to 1.0)
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#f-score
     */
    public function toHaveFScore(string|array $expected, ?float $threshold = null): self
    {
        return $this->assert(new Assertion(
            type: 'f-score',
            value: $expected,
            threshold: $threshold,
        ));
    }

    /**
     * Assert that the perplexity is below a threshold.
     *
     * @param  float|null  $threshold  Maximum perplexity value
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#perplexity
     */
    public function toHavePerplexity(?float $threshold = null): self
    {
        return $this->assert(new Assertion(
            type: 'perplexity',
            threshold: $threshold,
        ));
    }

    /**
     * Assert that the normalized perplexity score meets the threshold.
     *
     * @param  float|null  $threshold  Minimum perplexity score (0.0 to 1.0)
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#perplexity-score
     */
    public function toHavePerplexityScore(?float $threshold = null): self
    {
        return $this->assert(new Assertion(
            type: 'perplexity-score',
            threshold: $threshold,
        ));
    }
}
