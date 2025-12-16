<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\EvaluationResult;

final class Promptfoo
{
    private static ?PromptfooConfiguration $config = null;

    public static function configuration(): PromptfooConfiguration
    {
        return self::$config ??= new PromptfooConfiguration;
    }

    public static function evaluate(Evaluation $evaluation): EvaluationResult
    {
        return (new PromptfooExecutor(self::configuration()))->evaluate($evaluation);
    }

    /**
     * @return string[]
     */
    public static function defaultProviders(): array
    {
        return self::configuration()->defaultProviders();
    }

    /**
     * @param  string[]  $defaultProviders
     */
    public static function setDefaultProviders(array $defaultProviders): void
    {
        self::$config = self::configuration()->withDefaultProviders($defaultProviders);
    }

    public static function setOutputFolder(?string $path): void
    {
        self::$config = self::configuration()->withOutputFolder($path);
    }

    public static function outputFolder(): ?string
    {
        return self::configuration()->outputFolder();
    }

    public static function shouldOutput(): bool
    {
        return self::configuration()->shouldOutput();
    }

    /**
     * Reset configuration to defaults. Useful for test isolation.
     */
    public static function reset(): void
    {
        self::$config = null;
    }
}
