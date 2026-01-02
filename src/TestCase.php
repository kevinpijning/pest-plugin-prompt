<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use BadMethodCallException;
use InvalidArgumentException;
use KevinPijning\Prompt\Concerns\CanEnclose;
use KevinPijning\Prompt\Concerns\CanUseAssertions;
use KevinPijning\Prompt\Helpers\AssertionGroupName;
use KevinPijning\Prompt\Helpers\SourceLocation;
use KevinPijning\Prompt\Internal\AssertionGroupRegistry;
use KevinPijning\Prompt\Internal\BuiltTestCase;
use KevinPijning\Prompt\Internal\TrackedAssertion;
use RuntimeException;

/**
 * @property-read TestCase $not
 */
class TestCase
{
    use CanEnclose;
    use CanUseAssertions;

    /** @var TrackedAssertion[] */
    private array $trackedAssertions = [];

    private bool $shouldNegateNextAssertion = false;

    /**
     * @param  array<string,mixed>  $variables
     */
    public function __construct(
        private readonly array $variables,
        private readonly Evaluation $evaluation,
    ) {}

    public function assert(Assertion $assertion): self
    {
        $finalAssertion = $this->shouldNegateNextAssertion
            ? $assertion->negate()
            : $assertion;

        $this->shouldNegateNextAssertion = false;

        $sourceLocation = SourceLocation::capture();
        $assertionIndex = count($this->trackedAssertions);

        // Attach internal ID for unambiguous source location mapping
        $assertionWithId = $sourceLocation instanceof SourceLocation
            ? $finalAssertion->withInternalId($sourceLocation->file, $sourceLocation->line, $assertionIndex)
            : $finalAssertion;

        $this->trackedAssertions[] = new TrackedAssertion(
            assertion: $assertionWithId,
            sourceLocation: $sourceLocation,
        );

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
            if (count($arguments) === 0) {
                $args = [];
            } elseif (count($arguments) === 1 && is_array($arguments[0])) {
                $args = $arguments[0];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Assertion group "%s" expects a single array argument.',
                    $groupName
                ));
            }

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
            trackedAssertions: $this->trackedAssertions,
        );
    }
}
