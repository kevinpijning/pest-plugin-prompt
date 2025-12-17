<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanHaveFunctionCalls
{
    /**
     * Assert that the function call matches the function's JSON schema.
     *
     * @param  array<string,mixed>|null  $schema  Optional JSON schema to validate against
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#is-valid-function-call
     */
    public function toHaveValidFunctionCall(?array $schema = null): self
    {
        return $this->assert(new Assertion(
            type: 'is-valid-function-call',
            value: $schema,
        ));
    }

    /**
     * Assert that the OpenAI function call matches the function's JSON schema.
     *
     * @param  array<string,mixed>|null  $schema  Optional JSON schema to validate against
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#is-valid-openai-function-call
     */
    public function toHaveValidOpenaiFunctionCall(?array $schema = null): self
    {
        return $this->assert(new Assertion(
            type: 'is-valid-openai-function-call',
            value: $schema,
        ));
    }

    /**
     * Assert that all OpenAI tool calls match the tools JSON schema.
     *
     * @param  array<string,mixed>|null  $schema  Optional JSON schema to validate against
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#is-valid-openai-tools-call
     */
    public function toHaveValidOpenaiToolsCall(?array $schema = null): self
    {
        return $this->assert(new Assertion(
            type: 'is-valid-openai-tools-call',
            value: $schema,
        ));
    }

    /**
     * Assert that the F1 score comparing actual vs expected tool calls meets the threshold.
     *
     * @param  array<string,mixed>  $expected  Expected tool calls
     * @param  float|null  $threshold  Minimum F1 score (0.0 to 1.0)
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#tool-call-f1
     */
    public function toHaveToolCallF1(array $expected, ?float $threshold = null): self
    {
        return $this->assert(new Assertion(
            type: 'tool-call-f1',
            value: $expected,
            threshold: $threshold,
        ));
    }
}
