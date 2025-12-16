<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use InvalidArgumentException;
use KevinPijning\Prompt\Internal\Results\ComponentResult;
use KevinPijning\Prompt\Internal\Results\GradingResult;
use KevinPijning\Prompt\Internal\Results\Result;
use KevinPijning\Prompt\Promptfoo\Promptfoo;

/**
 * @internal
 */
class TestLifecycle
{
    public static function evaluate(): void
    {
        try {
            $evaluations = TestContext::getCurrentEvaluations();

            foreach ($evaluations as $evaluation) {
                $built = $evaluation->build();

                if ($built->testCases === []) {
                    continue;
                }

                self::handleEvaluationResult(Promptfoo::evaluate($evaluation));
            }
        } finally {
            // Always clear evaluations, even if an exception was thrown
            TestContext::clear();
        }
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

        // ANSI color codes
        $reset = "\033[0m";
        $bold = "\033[1m";
        $red = "\033[31m";
        $yellow = "\033[33m";
        $cyan = "\033[36m";
        $green = "\033[32m";
        $blue = "\033[34m";
        $dim = "\033[2m";

        $message = "{$bold}{$red}✗ Assertion failed{$reset} {$dim}(evaluated by promptfoo){$reset}\n";
        $message .= "{$bold}{$cyan}Provider:{$reset} {$blue}{$result->provider->id}{$reset} ";
        $message .= "{$bold}{$cyan}Assertion:{$reset} {$yellow}{$componentResult->assertion->type}{$reset} ";
        $message .= "{$bold}{$cyan}Expected:{$reset} {$green}".json_encode($componentResult->assertion->value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."{$reset}\n";
        $message .= "{$bold}{$cyan}Variables:{$reset} {$blue}".json_encode($result->vars, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."{$reset}\n";
        $message .= "{$bold}{$yellow}Reason:{$reset} {$red}{$componentResult->reason}{$reset}\n";
        $message .= "{$bold}{$cyan}Prompt:{$reset} {$blue}{$result->prompt->raw}{$reset}\n";

        return $message."{$bold}{$cyan}Actual output:{$reset} {$red}{$output}{$reset}\n";
    }
}
