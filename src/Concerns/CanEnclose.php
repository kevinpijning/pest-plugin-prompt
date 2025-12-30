<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use InvalidArgumentException;

trait CanEnclose
{
    /**
     * @param  callable(self): void|class-string  $callback
     */
    public function to(callable|string $callback): self
    {
        if (is_string($callback)) {
            if (! class_exists($callback)) {
                throw new InvalidArgumentException("Class {$callback} does not exist.");
            }

            if (! method_exists($callback, '__invoke')) {
                throw new InvalidArgumentException("Class {$callback} must be callable or an invokable class.");
            }

            $callback = new $callback;
        }

        $callback($this);

        return $this;
    }

    /**
     * @param  callable(self): void|class-string  $callback
     */
    public function group(callable|string $callback): self
    {
        return $this->to($callback);
    }
}
