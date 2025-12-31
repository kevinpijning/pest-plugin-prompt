<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\Provider;

/**
 * @internal
 */
final class ProviderContext
{
    /**
     * @var array<string, Provider>
     */
    private static array $providers = [];

    public static function add(string $name, Provider $provider): Provider
    {
        self::$providers[$name] = $provider;

        return self::$providers[$name];
    }

    public static function has(string $name): bool
    {
        return isset(self::$providers[$name]);
    }

    public static function get(string $name): Provider
    {
        return self::$providers[$name];
    }
}
