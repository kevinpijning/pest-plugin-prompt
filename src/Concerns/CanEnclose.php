<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use InvalidArgumentException;
use KevinPijning\Prompt\Internal\TestContext;

trait CanEnclose
{
    /**
     * @param  callable(self): void|class-string|string  $expect
     * @param  array<int|string,mixed>  $args
     */
    public function to(callable|string $expect, array $args = []): self
    {
        if (is_string($expect)) {
            // First, try to resolve as a named assertion group
            if (TestContext::hasAssertionGroup($expect)) {
                TestContext::getAssertionGroup($expect)->apply($this, $args);

                return $this;
            }

            // Fallback to existing invokable class FQN behavior
            if (! class_exists($expect)) {
                throw new InvalidArgumentException("Class {$expect} does not exist.");
            }

            if (! method_exists($expect, '__invoke')) {
                throw new InvalidArgumentException("Class {$expect} must be callable or an invokable class.");
            }

            $expect = new $expect;
        }

        // For non-string callables, preserve existing behavior and ignore $args
        $expect($this);

        return $this;
    }

    /**
     * @param  callable(self): void|class-string|string  $expectations
     * @param  array<int|string,mixed>  $args
     */
    public function group(callable|string $expectations, array $args = []): self
    {
        return $this->to($expectations, $args);
    }
}
