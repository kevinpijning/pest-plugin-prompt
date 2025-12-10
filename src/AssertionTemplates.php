<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use Pest\TestSuite;

final class AssertionTemplates
{
    private static bool $loaded = false;

    /**
     * @var array<string, array{type: string, value?: mixed, threshold?: float, options?: array<string, mixed>}>
     */
    private static array $templates = [];

    private static ?string $testPath = null;

    public static function apply(Evaluation $evaluation): void
    {
        if (! self::$loaded) {
            self::load();
        }

        foreach (self::$templates as $name => $template) {
            $evaluation->defineTemplate(
                name: $name,
                assertion: new Assertion(
                    type: $template['type'],
                    value: $template['value'] ?? null,
                    threshold: $template['threshold'] ?? null,
                    options: $template['options'] ?? null,
                )
            );
        }
    }

    public static function resetForTests(): void
    {
        self::$loaded = false;
        self::$templates = [];
    }

    /**
     * @internal For tests only.
     */
    public static function setTestPath(?string $path): void
    {
        self::$testPath = $path;
        self::$loaded = false;
    }

    private static function load(): void
    {
        $resolvedPath = self::$testPath ?? self::defaultPath();

        if ($resolvedPath === null || ! file_exists($resolvedPath)) {
            self::$loaded = true;

            return;
        }

        $templates = include $resolvedPath;

        if (is_array($templates)) {
            self::$templates = $templates;
        }

        self::$loaded = true;
    }

    private static function defaultPath(): ?string
    {
        if (! class_exists(TestSuite::class)) {
            return null;
        }

        $root = TestSuite::getInstance()->rootPath ?? null;

        if ($root === null) {
            return null;
        }

        return rtrim($root, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'prompt-assertion-templates.php';
    }
}
