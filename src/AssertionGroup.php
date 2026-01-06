<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Concerns\CanUseAssertions;
use KevinPijning\Prompt\Concerns\CollectsAssertions;

/**
 * @property-read AssertionGroup $not
 */
final class AssertionGroup
{
    use CanUseAssertions;
    use CollectsAssertions;

    /**
     * @var callable|null
     */
    private readonly mixed $callback;

    public function __construct(
        ?callable $callback = null,
    ) {
        $this->callback = $callback;
    }

    /**
     * @return Assertion[]
     */
    public function getAssertions(): array
    {
        return $this->assertions;
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
