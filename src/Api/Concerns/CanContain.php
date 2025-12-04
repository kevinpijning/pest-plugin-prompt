<?php

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;

trait CanContain
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function toContain(string $contains, bool $strict = false, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            $strict ? 'contains' : 'icontains',
            $contains,
            $threshold,
            $options,
        ));
    }

    /**
     * @param  string[]  $contains
     * @param  array<string,mixed>  $options
     */
    public function toContainAll(array $contains, bool $strict = false, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            $strict ? 'contains-all' : 'icontains-all',
            $contains,
            $threshold,
            $options,
        ));
    }

    /**
     * @param  string[]  $contains
     * @param  array<string,mixed>  $options
     */
    public function toContainAny(array $contains, bool $strict = false, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            $strict ? 'contains-any' : 'icontains-any',
            $contains,
            $threshold,
            $options,
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
