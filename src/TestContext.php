<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Api\Evaluation;

class TestContext
{
    /** @var Evaluation[] */
    private static array $evaluations = [];

    /**
     * @return Evaluation[]
     */
    public static function getCurrentEvaluations(): array
    {
        return self::$evaluations;
    }

    public static function addEvaluation(Evaluation $evaluation): void
    {
        self::$evaluations[] = $evaluation;
    }

    public static function clear(): void
    {
        self::$evaluations = [];
    }
}
