<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Concerns\CanUseAssertions;

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
     * @param  array<int|string,mixed>  $args
     */
    public function apply(TestCase $testCase, array $args = []): void
    {
        if ($this->callback !== null) {
            ($this->callback)($testCase, ...array_values($args));

            return;
        }

        foreach ($this->assertions as $assertion) {
            $testCase->assert($assertion);
        }
    }
}
