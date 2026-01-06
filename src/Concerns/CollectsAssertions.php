<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use BadMethodCallException;
use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Helpers\AssertionGroupName;
use KevinPijning\Prompt\Internal\AssertionGroupRegistry;
use RuntimeException;

/**
 * @property-read self $not
 */
trait CollectsAssertions
{
    /** @var Assertion[] */
    private array $assertions = [];

    private bool $shouldNegateNextAssertion = false;

    public function assert(Assertion $assertion): self
    {
        if ($this->shouldNegateNextAssertion) {
            $this->shouldNegateNextAssertion = false;
            $assertion = $assertion->negate();
        }

        $this->assertions[] = $assertion;

        return $this;
    }

    public function not(): self
    {
        $this->shouldNegateNextAssertion = ! $this->shouldNegateNextAssertion;

        return $this;
    }

    public function __get(string $name): mixed
    {
        if ($name === 'not') {
            return $this->not();
        }

        throw new RuntimeException(sprintf('Undefined property: %s::$%s', static::class, $name));
    }

    /**
     * @param  array<int,mixed>  $arguments
     */
    public function __call(string $name, array $arguments = []): self
    {
        $groupName = AssertionGroupName::fromMethodName($name);

        if ($groupName !== null && AssertionGroupRegistry::has($groupName)) {
            AssertionGroupRegistry::get($groupName)->apply($this, ...array_values($arguments));

            return $this;
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            static::class,
            $name
        ));
    }
}
