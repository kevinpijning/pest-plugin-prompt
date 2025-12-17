<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanHaveCustomValidation
{
    /**
     * Assert that the output passes validation by the provided JavaScript function.
     * The function receives (output, context) and should return true/false or a score.
     *
     * @param  string  $code  JavaScript code to execute
     * @param  float|null  $threshold  Minimum score threshold if function returns a number
     * @param  array<string,mixed>|null  $config  Additional configuration passed to the function
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/javascript/
     */
    public function toPassJavascript(string $code, ?float $threshold = null, ?array $config = null): self
    {
        return $this->assert(new Assertion(
            type: 'javascript',
            value: $code,
            threshold: $threshold,
            config: $config,
        ));
    }

    /**
     * Assert that the output passes validation by the provided Python function.
     * The function receives (output, context) and should return true/false or a score.
     *
     * @param  string  $code  Python code or file path to execute
     * @param  float|null  $threshold  Minimum score threshold if function returns a number
     * @param  array<string,mixed>|null  $config  Additional configuration passed to the function
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/python/
     */
    public function toPassPython(string $code, ?float $threshold = null, ?array $config = null): self
    {
        return $this->assert(new Assertion(
            type: 'python',
            value: $code,
            threshold: $threshold,
            config: $config,
        ));
    }

    /**
     * Assert that the webhook returns {pass: true}.
     *
     * @param  string  $url  Webhook URL to call
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#webhook
     */
    public function toPassWebhook(string $url): self
    {
        return $this->assert(new Assertion(
            type: 'webhook',
            value: $url,
        ));
    }
}
