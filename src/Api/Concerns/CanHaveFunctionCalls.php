<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;

trait CanHaveFunctionCalls
{
    /**
     * @param  array<string,mixed>|null  $schema
     * @param  array<string,mixed>  $options
     */
    public function toHaveValidFunctionCall(?array $schema = null, array $options = []): self
    {
        $assertionOptions = $options;
        if ($schema !== null) {
            $assertionOptions['schema'] = $schema;
        }

        return $this->assert(new Assertion(
            type: 'is-valid-function-call',
            value: null,
            threshold: null,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>|null  $schema
     * @param  array<string,mixed>  $options
     */
    public function toHaveValidOpenaiFunctionCall(?array $schema = null, array $options = []): self
    {
        $assertionOptions = $options;
        if ($schema !== null) {
            $assertionOptions['schema'] = $schema;
        }

        return $this->assert(new Assertion(
            type: 'is-valid-openai-function-call',
            value: null,
            threshold: null,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>|null  $schema
     * @param  array<string,mixed>  $options
     */
    public function toHaveValidOpenaiToolsCall(?array $schema = null, array $options = []): self
    {
        $assertionOptions = $options;
        if ($schema !== null) {
            $assertionOptions['schema'] = $schema;
        }

        return $this->assert(new Assertion(
            type: 'is-valid-openai-tools-call',
            value: null,
            threshold: null,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>  $expected
     * @param  array<string,mixed>  $options
     */
    public function toHaveToolCallF1(array $expected, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'tool-call-f1',
            value: $expected,
            threshold: $threshold,
            options: $options,
        ));
    }
}
