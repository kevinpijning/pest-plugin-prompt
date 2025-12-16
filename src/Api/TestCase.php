<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api;

use KevinPijning\Prompt\Api\Concerns\CanBeClassified;
use KevinPijning\Prompt\Internal\BuiltTestCase;
use KevinPijning\Prompt\Api\Concerns\CanBeJudged;
use KevinPijning\Prompt\Api\Concerns\CanBeRefused;
use KevinPijning\Prompt\Api\Concerns\CanBeScored;
use KevinPijning\Prompt\Api\Concerns\CanBeSimilar;
use KevinPijning\Prompt\Api\Concerns\CanBeValid;
use KevinPijning\Prompt\Api\Concerns\CanContain;
use KevinPijning\Prompt\Api\Concerns\CanEnclose;
use KevinPijning\Prompt\Api\Concerns\CanEqual;
use KevinPijning\Prompt\Api\Concerns\CanHaveCustomValidation;
use KevinPijning\Prompt\Api\Concerns\CanHaveFinishReason;
use KevinPijning\Prompt\Api\Concerns\CanHaveFunctionCalls;
use KevinPijning\Prompt\Api\Concerns\CanHavePerformance;
use KevinPijning\Prompt\Api\Concerns\CanHaveTraces;
use KevinPijning\Prompt\Api\Concerns\CanMatch;
use RuntimeException;

/**
 * @property-read TestCase $not
 */
class TestCase
{
    use CanBeClassified, CanBeJudged, CanBeRefused, CanBeScored, CanBeSimilar, CanBeValid, CanContain, CanEnclose, CanEqual, CanHaveCustomValidation, CanHaveFinishReason, CanHaveFunctionCalls, CanHavePerformance, CanHaveTraces, CanMatch;

    /** @var Assertion[] */
    private array $assertions = [];

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
        if (! $this->shouldNegateNextAssertion) {
            $this->assertions[] = $assertion;

            return $this;
        }

        $this->shouldNegateNextAssertion = false;
        $this->assertions[] = $assertion->negate();

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
     * @param  array<string,mixed>  $variables
     * @param  callable(TestCase): void|null  $callback
     */
    public function expect(array $variables, ?callable $callback = null): self
    {
        if (is_callable($callback)) {
            $this->to($callback);
        }

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
