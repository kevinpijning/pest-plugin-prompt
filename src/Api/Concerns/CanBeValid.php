<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;

trait CanBeValid
{
    /**
     * @param  array<string,mixed>|null  $schema
     * @param  array<string,mixed>  $options
     */
    public function toBeJson(?array $schema = null, array $options = []): self
    {
        $assertionOptions = $options;
        if ($schema !== null) {
            $assertionOptions['schema'] = $schema;
        }

        return $this->assert(new Assertion(
            type: 'is-json',
            value: null,
            threshold: null,
            options: $assertionOptions,
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
     * @param  array<string,mixed>|null  $authorityList
     * @param  array<string,mixed>  $options
     */
    public function toBeSql(?array $authorityList = null, array $options = []): self
    {
        $assertionOptions = $options;
        if ($authorityList !== null) {
            $assertionOptions['authorityList'] = $authorityList;
        }

        return $this->assert(new Assertion(
            type: 'is-sql',
            value: null,
            threshold: null,
            options: $assertionOptions,
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
