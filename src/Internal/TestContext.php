<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Provider;

/**
 * @internal
 */
class TestContext
{
    /** @var Evaluation[] */
    private static array $evaluations = [];

    /**
     * @var array<string, Provider>
     */
    private static array $providers = [];

    /**
     * @return Evaluation[]
     */
    public static function getCurrentEvaluations(): array
    {
        return self::$evaluations;
    }

    public static function addEvaluation(Evaluation $evaluation): Evaluation
    {
        self::$evaluations[] = $evaluation;

        return $evaluation;
    }

    public static function clear(): void
    {
        self::$evaluations = [];
    }

    public static function addProvider(string $name, Provider $provider): Provider
    {
        self::$providers[$name] = $provider;

        return self::$providers[$name];
    }

    public static function hasProvider(string $name): bool
    {
        return isset(self::$providers[$name]);
    }

    public static function getProvider(string $name): Provider
    {
        return self::$providers[$name];
    }
}
