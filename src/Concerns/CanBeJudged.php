<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeJudged
{
    /**
     * Assert that the output passes LLM-based evaluation using the provided rubric.
     * The rubric describes what makes a good response.
     *
     * @param  string  $rubric  The rubric/criteria to evaluate the output against
     * @param  float|null  $threshold  Minimum score to pass (0.0 to 1.0)
     * @param  string|null  $provider  LLM provider to use for evaluation (e.g., "openai:gpt-4")
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/model-graded/llm-rubric/
     */
    public function toBeJudged(string $rubric, ?float $threshold = null, ?string $provider = null): self
    {
        return $this->assert(new Assertion(
            type: 'llm-rubric',
            value: $rubric,
            threshold: $threshold,
            provider: $provider,
        ));
    }
}
