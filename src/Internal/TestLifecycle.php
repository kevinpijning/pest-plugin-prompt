<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use InvalidArgumentException;
use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Exceptions\PromptAssertionFailedException;
use KevinPijning\Prompt\Helpers\SourceLocation;
use KevinPijning\Prompt\Internal\Results\ComponentResult;
use KevinPijning\Prompt\Internal\Results\GradingResult;
use KevinPijning\Prompt\Internal\Results\Result;
use KevinPijning\Prompt\Promptfoo\Promptfoo;
use PHPUnit\Framework\Assert;

/**
 * @internal
 */
class TestLifecycle
{
    /** @var TrackedAssertion[] */
    private static array $currentTrackedAssertions = [];

    public static function evaluate(): void
    {
        try {
            $evaluations = EvaluationRegistry::getCurrentEvaluations();

            foreach ($evaluations as $evaluation) {
                $built = $evaluation->build();

                if ($built->testCases === []) {
                    continue;
                }

                self::$currentTrackedAssertions = self::collectTrackedAssertions($built);

                self::handleEvaluationResult(Promptfoo::evaluate($evaluation));
            }
        } finally {
            EvaluationRegistry::clear();
            self::$currentTrackedAssertions = [];
        }
    }

    /**
     * @return TrackedAssertion[]
     */
    private static function collectTrackedAssertions(BuiltEvaluation $built): array
    {
        $tracked = [];

        // Include default test case assertions (from alwaysExpect())
        if ($built->defaultTestCase instanceof BuiltTestCase) {
            foreach ($built->defaultTestCase->trackedAssertions as $trackedAssertion) {
                $tracked[] = $trackedAssertion;
            }
        }

        // Include assertions from all test cases
        foreach ($built->testCases as $testCase) {
            foreach ($testCase->trackedAssertions as $trackedAssertion) {
                $tracked[] = $trackedAssertion;
            }
        }

        return $tracked;
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
        self::countAssertion();

        if ($componentResult->pass) {
            return;
        }

        $message = self::buildFailureMessage($componentResult, $result);
        $sourceLocation = self::findSourceLocation($componentResult->assertion);

        throw new PromptAssertionFailedException($sourceLocation, $message);
    }

    private static function findSourceLocation(?Assertion $returnedAssertion): SourceLocation
    {
        if (! $returnedAssertion instanceof Assertion) {
            return new SourceLocation(__FILE__, __LINE__);
        }

        // 1. Prefer ID-based matching (unambiguous)
        $returnedId = $returnedAssertion->getInternalId();

        if ($returnedId !== null) {
            foreach (self::$currentTrackedAssertions as $tracked) {
                $trackedId = $tracked->assertion->getInternalId();
                if ($trackedId === $returnedId && $tracked->sourceLocation instanceof SourceLocation) {
                    return $tracked->sourceLocation;
                }
            }
        }

        // 2. Fallback: type + value match (for older outputs / edge cases)
        foreach (self::$currentTrackedAssertions as $tracked) {
            if ($tracked->matches($returnedAssertion) && $tracked->sourceLocation instanceof SourceLocation) {
                return $tracked->sourceLocation;
            }
        }

        // 3. Unknown source
        return new SourceLocation(__FILE__, __LINE__);
    }

    private static function buildFailureMessage(ComponentResult $componentResult, Result $result): string
    {
        $output = self::encodeOutput($result->response->output ?? '(no response available)');

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

        if ($componentResult->assertion instanceof Assertion) {
            $message .= "{$bold}{$cyan}Assertion:{$reset} {$yellow}{$componentResult->assertion->type}{$reset} ";
            $message .= "{$bold}{$cyan}Expected:{$reset} {$green}".json_encode($componentResult->assertion->value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."{$reset}\n";
        } else {
            $message .= "\n";
        }

        $message .= "{$bold}{$cyan}Variables:{$reset} {$blue}".json_encode($result->vars, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."{$reset}\n";
        $message .= "{$bold}{$yellow}Reason:{$reset} {$red}{$componentResult->reason}{$reset}\n";
        $message .= "{$bold}{$cyan}Prompt:{$reset} {$blue}{$result->prompt->raw}{$reset}\n";

        return $message."{$bold}{$cyan}Actual output:{$reset} {$red}{$output}{$reset}\n";
    }

    /**
     * @param  array<string,mixed>|string  $output
     */
    private static function encodeOutput(array|string $output): string
    {
        if (is_array($output)) {
            return json_encode($output, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        }

        return $output;
    }

    /**
     * Increment PHPUnit's assertion counter to properly track evaluated assertions.
     */
    private static function countAssertion(): void
    {
        Assert::assertCount(0, []);
    }
}
