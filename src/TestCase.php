<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use BadMethodCallException;
use KevinPijning\Prompt\Concerns\CanUseAssertions;
use KevinPijning\Prompt\Concerns\CollectsAssertions;
use KevinPijning\Prompt\Helpers\AssertionGroupName;
use KevinPijning\Prompt\Internal\AssertionGroupRegistry;
use KevinPijning\Prompt\Internal\BuiltTestCase;

/**
 * @property-read TestCase $not
 */
class TestCase
{
    use CanUseAssertions;
    use CollectsAssertions;

    /**
     * @param  array<string,mixed>  $variables
     */
    public function __construct(
        private readonly array $variables,
        private readonly Evaluation $evaluation,
    ) {}

    /**
     * @param  array<int,mixed>  $arguments
     */
    public function __call(string $name, array $arguments): self
    {
        $groupName = AssertionGroupName::fromMethodName($name);

        if ($groupName !== null && AssertionGroupRegistry::has($groupName)) {
            $args = $this->resolveAssertionGroupArguments($groupName, $arguments);

            AssertionGroupRegistry::get($groupName)->apply($this, $args);

            return $this;
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            static::class,
            $name
        ));
    }

    /**
     * @param  array<string,mixed>  $variables
     * @param  callable(TestCase): void|null  $callback
     */
    public function expect(array $variables, ?callable $callback = null): self
    {
        return $this->evaluation->expect($variables, $callback);
    }

    /**
     * @param  array<string,mixed>  $variables
     * @param  callable(TestCase): void|null  $callback
     */
    public function and(array $variables, ?callable $callback = null): self
    {
        return $this->expect($variables, $callback);
    }

    public function build(): BuiltTestCase
    {
        return new BuiltTestCase(
            variables: $this->variables,
            assertions: $this->assertions,
        );
    }
}
