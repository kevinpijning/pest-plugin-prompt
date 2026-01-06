<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Concerns\CanUseAssertions;
use KevinPijning\Prompt\Concerns\CollectsAssertions;
use KevinPijning\Prompt\Contracts\AssertsPrompts;
use KevinPijning\Prompt\Helpers\ArgumentBinder;

/**
 * @property-read AssertionGroup $not
 */
final class AssertionGroup implements AssertsPrompts
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
     * @param  array<int|string,mixed>  $args
     */
    public function apply(AssertsPrompts $target, array $args = []): void
    {
        if ($this->callback !== null) {
            $bound = ArgumentBinder::bind($this->callback, $args);
            ($this->callback)($target, ...$bound);

            return;
        }

        foreach ($this->assertions as $assertion) {
            $target->assert($assertion);
        }
    }
}
