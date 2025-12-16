<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

/**
 * @internal
 */
trait Promptable // @phpstan-ignore-line
{
    /**
     * Example description.
     */
    public function prompt(string ...$prompts): Evaluation
    {
        return prompt(...$prompts);
    }

    public function provider(string $name, ?callable $config = null): Provider
    {
        return provider($name, $config);
    }
}
