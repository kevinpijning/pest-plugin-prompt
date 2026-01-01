<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use Closure;
use InvalidArgumentException;
use KevinPijning\Prompt\Concerns\CanUseAssertions;
use ReflectionFunction;
use ReflectionNamedType;

final class AssertionGroup
{
    use CanUseAssertions;

    /**
     * @var callable|null
     */
    private readonly mixed $callback;

    /**
     * @var Assertion[]
     */
    private array $assertions = [];

    public function __construct(
        public readonly string $name,
        ?callable $callback = null,
    ) {
        $this->callback = $callback;
    }

    public function assert(Assertion $assertion): self
    {
        $this->assertions[] = $assertion;

        return $this;
    }

    /**
     * Apply this assertion group to a test case.
     *
     * @param  array<int|string,mixed>  $args
     */
    public function apply(TestCase $testCase, array $args = []): void
    {
        if ($this->callback !== null) {
            $this->invokeCallback($testCase, $args);

            return;
        }

        foreach ($this->assertions as $assertion) {
            $testCase->assert($assertion);
        }
    }

    /**
     * Invoke the callback with strict argument binding.
     *
     * @param  array<int|string,mixed>  $args
     */
    private function invokeCallback(TestCase $testCase, array $args): void
    {
        /** @var callable $callable */
        $callable = $this->callback;

        $closure = Closure::fromCallable($callable);
        $reflection = new ReflectionFunction($closure);
        $parameters = $reflection->getParameters();

        $boundArgs = [];
        $usedKeys = [];
        $usesGroupInstance = false;
        $firstIsContext = false;

        if (isset($parameters[0])) {
            $firstParam = $parameters[0];
            $type = $firstParam->getType();

            if ($type instanceof ReflectionNamedType) {
                $name = $type->getName();

                if ($name === self::class) {
                    // AssertionGroup $group, ...
                    $firstIsContext = true;
                    $usesGroupInstance = true;
                    $boundArgs[] = $this;
                } elseif ($name === TestCase::class) {
                    // TestCase $tc, ...
                    $firstIsContext = true;
                    $boundArgs[] = $testCase;
                }
            }

        }

        if ($usesGroupInstance) {
            $this->assertions = [];
        }

        foreach ($parameters as $index => $parameter) {
            if ($firstIsContext && $index === 0) {
                // Context argument already bound
                continue;
            }

            $paramName = $parameter->getName();
            $value = null;
            $found = false;

            // Try to find value by name (associative array) or position (list)
            if (array_is_list($args)) {
                $argIndex = $firstIsContext ? $index - 1 : $index;
                if (array_key_exists($argIndex, $args)) {
                    $value = $args[$argIndex];
                    $found = true;
                    $usedKeys[] = $argIndex;
                }
            } elseif (array_key_exists($paramName, $args)) {
                $value = $args[$paramName];
                $found = true;
                $usedKeys[] = $paramName;
            }

            // Handle missing values
            if (! $found) {
                if ($parameter->isDefaultValueAvailable()) {
                    $value = $parameter->getDefaultValue();
                } elseif ($parameter->allowsNull()) {
                    $value = null;
                } else {
                    throw new InvalidArgumentException(sprintf(
                        'Missing required argument "%s" for assertion group "%s".',
                        $paramName,
                        $this->name
                    ));
                }
            }

            $boundArgs[] = $value;
        }

        // Check for extra arguments (strict mode)
        $allKeys = array_keys($args);
        $extraKeys = array_diff($allKeys, $usedKeys);

        if ($extraKeys !== []) {
            $extraKeyNames = array_is_list($args)
                ? array_map(static fn (int $k): string => "position {$k}", $extraKeys)
                : $extraKeys;

            throw new InvalidArgumentException(sprintf(
                'Unknown argument(s) for assertion group "%s": %s',
                $this->name,
                implode(', ', $extraKeyNames)
            ));
        }

        $closure(...$boundArgs);

        if ($usesGroupInstance) {
            foreach ($this->assertions as $assertion) {
                $testCase->assert($assertion);
            }
        }
    }
}
