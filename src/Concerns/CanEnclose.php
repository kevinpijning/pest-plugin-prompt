<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use InvalidArgumentException;
use KevinPijning\Prompt\Internal\TestContext;

trait CanEnclose
{
    /**
     * @param  callable(self): void|class-string|string  $callback
     * @param  array<int|string,mixed>  $args
     */
    public function to(callable|string $callback, array $args = []): self
    {
        if (is_string($callback)) {
            // First, try to resolve as a named assertion group
            if (TestContext::hasAssertionGroup($callback)) {
                TestContext::getAssertionGroup($callback)->apply($this, $args);

                return $this;
            }

            // Fallback to existing invokable class FQN behavior
            if (! class_exists($callback)) {
                throw new InvalidArgumentException("Class {$callback} does not exist.");
            }

            if (! method_exists($callback, '__invoke')) {
                throw new InvalidArgumentException("Class {$callback} must be callable or an invokable class.");
            }

            $callback = new $callback;
        }

        // For non-string callables, preserve existing behavior and ignore $args
        $callback($this);

        return $this;
    }

    /**
     * @param  callable(self): void|class-string|string  $callback
     * @param  array<int|string,mixed>  $args
     */
    public function group(callable|string $callback, array $args = []): self
    {
        return $this->to($callback, $args);
    }
}
