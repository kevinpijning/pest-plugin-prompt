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
     * Create or register a provider.
     *
     * When called without arguments, returns a ProviderFactory for extension registration.
     * When called with a name, registers and returns a Provider instance.
     */
    public function provider(?string $name = null, ?callable $config = null): Provider|ProviderFactory
    {
        return provider($name, $config);
    }
}
