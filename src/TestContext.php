<?php

declare(strict_types=1);

namespace Pest\Prompt;

use Pest\Prompt\Api\Evaluation;

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
