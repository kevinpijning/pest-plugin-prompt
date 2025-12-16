<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use KevinPijning\Prompt\Evaluation;

class Promptfoo
{
    private static string $promptfooCommand = 'npx promptfoo@latest';

    private static int $promptfooTimeout = 300;

    /**
     * @var string[]
     */
    private static array $defaultProviders = ['openai:gpt-4o-mini'];

    private static ?string $outputFolder = null;

    public static function initialize(): PromptfooClient
    {
        return new PromptfooClient(self::$promptfooCommand, self::$promptfooTimeout);
    }

    public static function evaluate(Evaluation $valuation): EvaluationResult
    {
        return self::initialize()->evaluate($valuation);
    }

    /**
     * @return string[]
     */
    public static function defaultProviders(): array
    {
        return self::$defaultProviders;
    }

    /**
     * @param  string[]  $defaultProviders
     */
    public static function setDefaultProviders(array $defaultProviders): void
    {
        self::$defaultProviders = $defaultProviders;
    }

    public static function setOutputFolder(?string $path): void
    {
        self::$outputFolder = $path;
    }

    public static function outputFolder(): ?string
    {
        return self::$outputFolder;
    }

    public static function shouldOutput(): bool
    {
        return self::outputFolder() !== null;
    }
}
