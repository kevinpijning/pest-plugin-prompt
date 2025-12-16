<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api;

use KevinPijning\Prompt\Api\Concerns\CanBeClassified;
use KevinPijning\Prompt\Api\Concerns\CanBeJudged;
use KevinPijning\Prompt\Api\Concerns\CanBeRefused;
use KevinPijning\Prompt\Api\Concerns\CanBeScored;
use KevinPijning\Prompt\Api\Concerns\CanBeSimilar;
use KevinPijning\Prompt\Api\Concerns\CanBeValid;
use KevinPijning\Prompt\Api\Concerns\CanContain;
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
    use CanBeClassified, CanBeJudged, CanBeRefused, CanBeScored, CanBeSimilar, CanBeValid, CanContain, CanEqual, CanHaveCustomValidation, CanHaveFinishReason, CanHaveFunctionCalls, CanHavePerformance, CanHaveTraces, CanMatch;

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

    /**
     * @return Assertion[]
     */
    public function assertions(): array
    {
        return $this->assertions;
    }

    /**
     * @return array<string,mixed>
     */
    public function variables(): array
    {
        return $this->variables;
    }

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
     */
    public function expect(array $variables): self
    {
        return $this->evaluation->expect($variables);
    }

    /**
     * @param  array<string,mixed>  $variables
     */
    public function and(array $variables): self
    {
        return $this->expect($variables);
    }
}
