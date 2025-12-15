<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;

trait CanHaveCustomValidation
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toPassJavascript(string $code, array $options = []): self
    {
        $assertionOptions = array_merge($options, ['code' => $code]);

        return $this->assert(new Assertion(
            type: 'javascript',
            value: $code,
            threshold: null,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toPassPython(string $code, array $options = []): self
    {
        $assertionOptions = array_merge($options, ['code' => $code]);

        return $this->assert(new Assertion(
            type: 'python',
            value: $code,
            threshold: null,
            options: $assertionOptions,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toPassWebhook(string $url, array $options = []): self
    {
        $assertionOptions = array_merge($options, ['url' => $url]);

        return $this->assert(new Assertion(
            type: 'webhook',
            value: $url,
            threshold: null,
            options: $assertionOptions,
        ));
    }
}

