<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

/**
 * Represents a promptfoo assertion configuration.
 *
 * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/#assertion-properties
 */
class Assertion
{
    /**
     * Internal config key namespace for pest-plugin-prompt metadata.
     */
    public const string INTERNAL_CONFIG_KEY = '__pest_plugin_prompt';

    /**
     * Key for the unique assertion ID within the internal config namespace.
     */
    public const string INTERNAL_ASSERTION_ID_KEY = 'assertion_id';

    /**
     * @param  string  $type  Type of assertion
     * @param  mixed  $value  The expected value, if applicable
     * @param  float|null  $threshold  Threshold value for similar, cost, javascript, python assertions
     * @param  float|null  $weight  How heavily to weigh the assertion (default: 1.0)
     * @param  string|null  $provider  LLM provider for similarity, llm-rubric, model-graded-* assertions
     * @param  string|string[]|null  $rubricPrompt  Model-graded LLM prompt
     * @param  array<string, mixed>|null  $config  External mapping passed to custom javascript/python assertions
     * @param  string|null  $transform  Process the output before running the assertion
     * @param  string|null  $metric  Tag that appears in the web UI as a named metric
     */
    public function __construct(
        public readonly string $type,
        public readonly mixed $value = null,
        public readonly ?float $threshold = null,
        public readonly ?float $weight = null,
        public readonly ?string $provider = null,
        public readonly string|array|null $rubricPrompt = null,
        public readonly ?array $config = null,
        public readonly ?string $transform = null,
        public readonly ?string $metric = null,
    ) {}

    public function negate(): self
    {
        return new self(
            type: $this->negateType($this->type),
            value: $this->value,
            threshold: $this->threshold,
            weight: $this->weight,
            provider: $this->provider,
            rubricPrompt: $this->rubricPrompt,
            config: $this->config,
            transform: $this->transform,
            metric: $this->metric,
        );
    }

    private function negateType(string $type): string
    {
        $prefix = 'not-';

        if (str_starts_with($type, $prefix)) {
            return substr($type, strlen($prefix));
        }

        return $prefix.$type;
    }

    /**
     * Create a new Assertion with an internal assertion ID embedded in config.
     *
     * @param  string  $file  The source file path
     * @param  int  $line  The source line number
     * @param  int  $index  The assertion index within the test case
     */
    public function withInternalId(string $file, int $line, int $index): self
    {
        $assertionId = sha1(sprintf('%s:%d:%d', $file, $line, $index));

        return new self(
            type: $this->type,
            value: $this->value,
            threshold: $this->threshold,
            weight: $this->weight,
            provider: $this->provider,
            rubricPrompt: $this->rubricPrompt,
            config: array_merge(
                $this->config ?? [],
                [self::INTERNAL_CONFIG_KEY => [self::INTERNAL_ASSERTION_ID_KEY => $assertionId]],
            ),
            transform: $this->transform,
            metric: $this->metric,
        );
    }

    /**
     * Get the internal assertion ID if present.
     */
    public function getInternalId(): ?string
    {
        return $this->config[self::INTERNAL_CONFIG_KEY][self::INTERNAL_ASSERTION_ID_KEY] ?? null;
    }
}
