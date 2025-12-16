<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeValid
{
    /**
     * @param  array<string,mixed>|null  $schema
     * @param  array<string,mixed>  $options
     */
    public function toBeJson(?array $schema = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'is-json',
            value: $schema,
            threshold: null,
            options: $options,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toBeHtml(array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'is-html',
            value: null,
            threshold: null,
            options: $options,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toBeSql(array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'is-sql',
            value: null,
            threshold: null,
            options: $options,
        ));
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toBeXml(array $options = []): self
    {
        return $this->assert(new Assertion(
            type: 'is-xml',
            value: null,
            threshold: null,
            options: $options,
        ));
    }
}
