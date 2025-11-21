<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo;

use Pest\Prompt\Api\Evaluation;

class Promptfoo
{
    private static string $promptfooCommand = 'npx playwright@latest';

    public static function initialize(): PromptfooClient
    {
        return new PromptfooClient(self::$promptfooCommand);
    }

    public static function evaluate(Evaluation $evaluationBuilder): EvaluationResult
    {
        return self::initialize()->evaluate($evaluationBuilder);
    }
}
