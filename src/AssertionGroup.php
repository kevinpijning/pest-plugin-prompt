<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use Closure;
use InvalidArgumentException;
use KevinPijning\Prompt\Concerns\CanUseAssertions;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;

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

        $context = $this->detectContextParameter($parameters, $testCase);

        if ($context['usesGroupInstance']) {
            $this->assertions = [];
        }

        $binding = $this->bindArguments($parameters, $args, $context['hasContextParam']);
        $boundArgs = array_merge($context['boundArgs'], $binding['boundArgs']);

        $this->validateNoExtraArguments($args, $binding['usedKeys']);

        $closure(...$boundArgs);

        if ($context['usesGroupInstance']) {
            $this->applyCollectedAssertions($testCase);
        }
    }

    /**
     * @param  ReflectionParameter[]  $parameters
     * @return array{hasContextParam: bool, usesGroupInstance: bool, boundArgs: array<int, mixed>}
     */
    private function detectContextParameter(array $parameters, TestCase $testCase): array
    {
        if (! isset($parameters[0])) {
            return ['hasContextParam' => false, 'usesGroupInstance' => false, 'boundArgs' => []];
        }

        $type = $parameters[0]->getType();

        if (! $type instanceof ReflectionNamedType) {
            return ['hasContextParam' => false, 'usesGroupInstance' => false, 'boundArgs' => []];
        }

        $typeName = $type->getName();

        if ($typeName === self::class) {
            return ['hasContextParam' => true, 'usesGroupInstance' => true, 'boundArgs' => [$this]];
        }

        if ($typeName === TestCase::class) {
            return ['hasContextParam' => true, 'usesGroupInstance' => false, 'boundArgs' => [$testCase]];
        }

        return ['hasContextParam' => false, 'usesGroupInstance' => false, 'boundArgs' => []];
    }

    /**
     * @param  ReflectionParameter[]  $parameters
     * @param  array<int|string,mixed>  $args
     * @return array{boundArgs: array<int, mixed>, usedKeys: array<int, int|string>}
     */
    private function bindArguments(array $parameters, array $args, bool $hasContextParam): array
    {
        $boundArgs = [];
        $usedKeys = [];

        foreach ($parameters as $index => $parameter) {
            if ($hasContextParam && $index === 0) {
                continue;
            }

            $paramName = $parameter->getName();
            $resolved = $this->resolveArgumentValue($parameter, $args, $index, $hasContextParam);

            $boundArgs[] = $resolved['value'];

            if ($resolved['usedKey'] !== null) {
                $usedKeys[] = $resolved['usedKey'];
            }
        }

        return ['boundArgs' => $boundArgs, 'usedKeys' => $usedKeys];
    }

    /**
     * @param  array<int|string,mixed>  $args
     * @return array{value: mixed, usedKey: int|string|null}
     */
    private function resolveArgumentValue(
        ReflectionParameter $parameter,
        array $args,
        int $index,
        bool $hasContextParam
    ): array {
        $paramName = $parameter->getName();

        if (array_is_list($args)) {
            $argIndex = $hasContextParam ? $index - 1 : $index;

            if (array_key_exists($argIndex, $args)) {
                return ['value' => $args[$argIndex], 'usedKey' => $argIndex];
            }
        } elseif (array_key_exists($paramName, $args)) {
            return ['value' => $args[$paramName], 'usedKey' => $paramName];
        }

        return ['value' => $this->getDefaultValue($parameter), 'usedKey' => null];
    }

    private function getDefaultValue(ReflectionParameter $parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        throw new InvalidArgumentException(sprintf(
            'Missing required argument "%s" for assertion group "%s".',
            $parameter->getName(),
            $this->name
        ));
    }

    /**
     * @param  array<int|string,mixed>  $args
     * @param  array<int, int|string>  $usedKeys
     */
    private function validateNoExtraArguments(array $args, array $usedKeys): void
    {
        $extraKeys = array_diff(array_keys($args), $usedKeys);

        if ($extraKeys === []) {
            return;
        }

        $extraKeyNames = array_is_list($args)
            ? array_map(static fn (int|string $k): string => "position {$k}", $extraKeys)
            : $extraKeys;

        throw new InvalidArgumentException(sprintf(
            'Unknown argument(s) for assertion group "%s": %s',
            $this->name,
            implode(', ', $extraKeyNames)
        ));
    }

    private function applyCollectedAssertions(TestCase $testCase): void
    {
        foreach ($this->assertions as $assertion) {
            $testCase->assert($assertion);
        }
    }
}
