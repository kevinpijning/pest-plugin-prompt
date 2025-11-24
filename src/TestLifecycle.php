<?php

declare(strict_types=1);

namespace Pest\Prompt;

use InvalidArgumentException;
use Pest\Prompt\Promptfoo\EvaluationResult;
use Pest\Prompt\Promptfoo\Promptfoo;
use Pest\Prompt\Promptfoo\Results\ComponentResult;
use Pest\Prompt\Promptfoo\Results\GradingResult;
use Pest\Prompt\Promptfoo\Results\Result;

class TestLifecycle
{
    public static function evaluate(): void
    {
        $evaluations = TestContext::getCurrentEvaluations();

        foreach ($evaluations as $evaluation) {
            if (empty($evaluation->testCases())) {
                continue;
            }

            self::handleEvaluationResult(Promptfoo::evaluate($evaluation));
        }

        TestContext::clear();
    }

    private static function handleEvaluationResult(EvaluationResult $evaluationResult): void
    {
        foreach ($evaluationResult->results as $result) {
            self::assertResult($result);
        }
    }

    private static function assertResult(Result $result): void
    {
        if ($result->error !== null && ! $result->gradingResult instanceof GradingResult) {
            throw new InvalidArgumentException($result->error);
        }

        if (! $result->gradingResult instanceof GradingResult) {
            throw new InvalidArgumentException('No grading result given');
        }

        foreach ($result->gradingResult->componentResults as $componentResult) {
            self::assertComponentResult($componentResult, $result);
        }
    }

    private static function assertComponentResult(ComponentResult $componentResult, Result $result): void
    {
        $message = self::buildFailureMessage($componentResult, $result);

        expect($componentResult->pass)->toBeTrue($message);
    }

    private static function buildFailureMessage(ComponentResult $componentResult, Result $result): string
    {
        $output = $result->response->output ?? '(no response available)';

        return sprintf(
            "Assertion failed (evaluated by promptfoo):\n".
            "Provider: %s\n".
            "Variables: %s\n".
            "Assertion type: %s\n".
            "Expected: %s\n".
            "Prompt: %s\n".
            "Actual output: %s\n".
            'Reason: %s',
            $result->provider->id,
            json_encode($result->vars, JSON_PRETTY_PRINT),
            $componentResult->assertion->type,
            json_encode($componentResult->assertion->value, JSON_PRETTY_PRINT),
            $result->prompt->raw,
            $output,
            $componentResult->reason
        );
    }
}
