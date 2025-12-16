<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

trait CanEnclose
{
    /**
     * @param  callable(self): void  $callback
     */
    public function to(callable $callback): self
    {
        $callback($this);

        return $this;
    }

    /**
     * @param  callable(self): void  $callback
     */
    public function group(callable $callback): self
    {
        return $this->to($callback);
    }
}
