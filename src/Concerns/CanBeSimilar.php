<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeSimilar
{
    /**
     * @param  string|string[]  $expected
     * @param  array<string,mixed>  $options
     */
    public function toBeSimilar(string|array $expected, ?float $threshold = null, ?string $provider = null, array $options = []): self
    {
        $assertionOptions = $options;
        if ($provider !== null) {
            $assertionOptions['provider'] = $provider;
        }

        return $this->assert(new Assertion(
            type: 'similar',
            value: $expected,
            threshold: $threshold,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toHaveLevenshtein(string $expected, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'levenshtein',
            value: $expected,
            threshold: $threshold,
            options: $options,
        ));
    }

    /**
     * @param  string|string[]  $expected
     * @param  array<string,mixed>  $options
     */
    public function toHaveRougeN(int $n, string|array $expected, ?float $threshold = null, array $options = []): self
    {
        $assertionOptions = array_merge($options, ['n' => $n]);

        return $this->assert(new Assertion(
            type: 'rouge-n',
            value: $expected,
            threshold: $threshold,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  string|string[]  $expected
     * @param  array<string,mixed>  $options
     */
    public function toHaveFScore(string|array $expected, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'f-score',
            value: $expected,
            threshold: $threshold,
            options: $options,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toHavePerplexity(?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'perplexity',
            value: null,
            threshold: $threshold,
            options: $options,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toHavePerplexityScore(?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'perplexity-score',
            value: null,
            threshold: $threshold,
            options: $options,
        ));
    }
}
