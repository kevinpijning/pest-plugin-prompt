<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanContain
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toContain(string $contains, bool $strict = false, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: $strict ? 'contains' : 'icontains',
            value: $contains,
            threshold: $threshold,
            options: $options,
        ));
    }

    /**
     * @param  string[]  $contains
     * @param  array<string,mixed>  $options
     */
    public function toContainAll(array $contains, bool $strict = false, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: $strict ? 'contains-all' : 'icontains-all',
            value: $contains,
            threshold: $threshold,
            options: $options,
        ));
    }

    /**
     * @param  string[]  $contains
     * @param  array<string,mixed>  $options
     */
    public function toContainAny(array $contains, bool $strict = false, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            type: $strict ? 'contains-any' : 'icontains-any',
            value: $contains,
            threshold: $threshold,
            options: $options,
        ));
    }

    public function toContainJson(): self
    {
        return $this->assert(new Assertion('contains-json'));
    }

    public function toContainHtml(): self
    {
        return $this->assert(new Assertion('contains-html'));
    }

    public function toContainSql(): self
    {
        return $this->assert(new Assertion('contains-sql'));
    }

    public function toContainXml(): self
    {
        return $this->assert(new Assertion('contains-xml'));
    }
}
