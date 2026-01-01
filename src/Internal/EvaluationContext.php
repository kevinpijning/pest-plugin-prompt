<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\Evaluation;

/**
 * @internal
 */
class EvaluationContext
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

    public static function addEvaluation(Evaluation $evaluation): Evaluation
    {
        self::$evaluations[] = $evaluation;

        return $evaluation;
    }

    public static function clear(): void
    {
        self::$evaluations = [];
    }
}
