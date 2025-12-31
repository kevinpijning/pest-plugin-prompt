<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\AssertionGroup;

/**
 * @internal
 */
final class AssertionGroupContext
{
    /**
     * @var array<string, AssertionGroup>
     */
    private static array $groups = [];

    public static function add(string $name, AssertionGroup $group): AssertionGroup
    {
        self::$groups[$name] = $group;

        return self::$groups[$name];
    }

    public static function has(string $name): bool
    {
        return isset(self::$groups[$name]);
    }

    public static function get(string $name): AssertionGroup
    {
        return self::$groups[$name];
    }
}
