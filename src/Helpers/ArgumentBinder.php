<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Helpers;

use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionParameter;

final class ArgumentBinder
{
    /**
     * @param  array<int|string,mixed>  $args
     * @return array<int,mixed>
     */
    public static function bind(callable $callback, array $args): array
    {
        $reflection = new ReflectionFunction(Closure::fromCallable($callback));
        $params = array_slice($reflection->getParameters(), 1);

        if (self::isPositional($args)) {
            /** @var array<int,mixed> $args */
            return self::bindPositional($args);
        }

        /** @var array<string,mixed> $args */
        return self::bindNamed($params, $args);
    }

    /**
     * @param  array<int|string,mixed>  $args
     */
    private static function isPositional(array $args): bool
    {
        if ($args === []) {
            return true;
        }

        return array_is_list($args);
    }

    /**
     * @param  array<int,mixed>  $args
     * @return array<int,mixed>
     */
    private static function bindPositional(array $args): array
    {
        return $args;
    }

    /**
     * @param  array<int, ReflectionParameter>  $params
     * @param  array<string,mixed>  $args
     * @return array<int,mixed>
     */
    private static function bindNamed(array $params, array $args): array
    {
        $bound = [];

        foreach ($params as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $args)) {
                $bound[] = $args[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $bound[] = $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException(
                    sprintf("Missing required argument '%s'", $name)
                );
            }
        }

        return $bound;
    }
}
