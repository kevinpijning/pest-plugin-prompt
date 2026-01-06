<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use BadMethodCallException;
use InvalidArgumentException;
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
    public function __call(string $name, array $arguments): self
    {
        $groupName = AssertionGroupName::fromMethodName($name);

        if ($groupName !== null && AssertionGroupRegistry::has($groupName)) {
            $args = $this->resolveAssertionGroupArguments($groupName, $arguments);
            $group = AssertionGroupRegistry::get($groupName);

            foreach ($group->getAssertions() as $assertion) {
                $this->assert($assertion);
            }

            return $this;
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            static::class,
            $name
        ));
    }

    /**
     * @param  array<int,mixed>  $arguments
     * @return array<int|string,mixed>
     */
    private function resolveAssertionGroupArguments(string $groupName, array $arguments): array
    {
        if (count($arguments) === 0) {
            return [];
        }

        if (count($arguments) === 1 && is_array($arguments[0])) {
            return $arguments[0];
        }

        throw new InvalidArgumentException(sprintf(
            'Assertion group "%s" expects a single array argument.',
            $groupName
        ));
    }
}
