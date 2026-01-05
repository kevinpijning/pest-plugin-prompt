<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use BadMethodCallException;
use Closure;

/**
 * Adds extension capabilities to a class, similar to Laravel's Macroable trait.
 *
 * Classes using this trait can register custom methods at runtime via extend().
 */
trait CanBeExtended
{
    /** @var array<string, Closure> */
    protected static array $extensions = [];

    /**
     * Register a custom extension method.
     */
    public static function extend(string $name, Closure $callback): void
    {
        static::$extensions[$name] = $callback;
    }

    /**
     * Check if an extension is registered.
     */
    public static function hasExtension(string $name): bool
    {
        return isset(static::$extensions[$name]);
    }

    /**
     * Remove all registered extensions.
     */
    public static function flushExtensions(): void
    {
        static::$extensions = [];
    }

    /**
     * Handle dynamic method calls to extensions.
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (! static::hasExtension($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $callback = static::$extensions[$method];

        $result = $callback($this, ...$parameters);

        return $result ?? $this;
    }
}
