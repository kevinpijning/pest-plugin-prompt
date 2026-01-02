<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use InvalidArgumentException;
use KevinPijning\Prompt\Internal\AssertionGroupRegistry;

trait CanEnclose
{
    /**
     * @param  callable(self): void|string  $expect
     * @param  array<int|string,mixed>  $args  Arguments for named assertion groups only; ignored for callables
     */
    public function to(callable|string $expect, array $args = []): self
    {
        if (is_string($expect)) {
            if (! AssertionGroupRegistry::has($expect)) {
                throw new InvalidArgumentException("Assertion group '{$expect}' not found. Register it using assertion().");
            }

            AssertionGroupRegistry::get($expect)->apply($this, $args);

            return $this;
        }

        $expect($this);

        return $this;
    }

    /**
     * @param  callable(self): void|string  $expectations
     * @param  array<int|string,mixed>  $args  Arguments for named assertion groups only; ignored for callables
     */
    public function group(callable|string $expectations, array $args = []): self
    {
        return $this->to($expectations, $args);
    }
}
