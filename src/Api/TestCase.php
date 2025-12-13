<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api;

use KevinPijning\Prompt\Api\Concerns\CanBeJudged;
use KevinPijning\Prompt\Api\Concerns\CanContain;
use RuntimeException;

/**
 * @mixin Assertion
 */
class TestCase
{
    use CanBeJudged, CanContain;

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

        $this->assertions[] = new Assertion(
            type: $this->negateAssertionType($assertion->type),
            value: $assertion->value,
            threshold: $assertion->threshold,
            options: $assertion->options,
        );

        return $this;
    }

    public function __get(string $name): mixed
    {
        if ($name === 'not') {
            $this->shouldNegateNextAssertion = ! $this->shouldNegateNextAssertion;

            return $this;
        }

        throw new RuntimeException(sprintf('Undefined property: %s::$%s', static::class, $name));
    }

    private function negateAssertionType(string $type): string
    {
        $prefix = 'not-';

        if (str_starts_with($type, $prefix)) {
            return substr($type, strlen($prefix));
        }

        return $prefix.$type;
    }

    /**
     * @param  array<string,mixed>  $variables
     */
    public function and(array $variables): self
    {
        return $this->evaluation->expect($variables);
    }
}
