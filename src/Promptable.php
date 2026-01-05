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

    /**
     * @param  (callable(Provider): Provider)|null  $config
     * @return ($name is null ? ProviderFactory : Provider)
     */
    public function provider(?string $name = null, ?callable $config = null): Provider|ProviderFactory
    {
        return provider($name, $config);
    }
}
