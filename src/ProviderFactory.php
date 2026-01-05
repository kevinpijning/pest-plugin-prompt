<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use Closure;

/**
 * Factory for Provider extension registration.
 *
 * Returned by provider() when called without arguments.
 */
final class ProviderFactory
{
    /**
     * Register a custom extension method for all Provider instances.
     */
    public function extend(string $name, Closure $callback): self
    {
        Provider::extend($name, $callback);

        return $this;
    }

    /**
     * Check if an extension is registered.
     */
    public function hasExtension(string $name): bool
    {
        return Provider::hasExtension($name);
    }

    /**
     * Remove all registered extensions.
     */
    public function flushExtensions(): void
    {
        Provider::flushExtensions();
    }
}
