<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Concerns\CanUseAssertions;
use KevinPijning\Prompt\Concerns\CollectsAssertions;
use KevinPijning\Prompt\Contracts\AssertsPrompts;

/**
 * @property-read AssertionGroup $not
 */
final class AssertionGroup implements AssertsPrompts
{
    use CollectsAssertions;
    use CanUseAssertions;

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
     * @param  array<int|string,mixed>  $args
     */
    public function apply(AssertsPrompts $target, array $args = []): void
    {
        if ($this->callback !== null) {
            ($this->callback)($target, ...array_values($args));

            return;
        }

        foreach ($this->assertions as $assertion) {
            $target->assert($assertion);
        }
    }
}
