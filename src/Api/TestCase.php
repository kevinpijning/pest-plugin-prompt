<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api;

use KevinPijning\Prompt\Api\Concerns\CanBeJudged;
use KevinPijning\Prompt\Api\Concerns\CanContain;
use KevinPijning\Prompt\Api\Concerns\CanEqual;

/**
 * @mixin Assertion
 */
class TestCase
{
    use CanBeJudged, CanContain, CanEqual;

    /** @var Assertion[] */
    private array $assertions = [];

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
        $this->assertions[] = $assertion;

        return $this;
    }

    /**
     * @param  array<string,mixed>  $variables
     */
    public function and(array $variables): self
    {
        return $this->evaluation->expect($variables);
    }
}
