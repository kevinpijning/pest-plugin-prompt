<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

class OutputPath
{
    private static ?string $outputPath = null;

    public static function set(string $path): void
    {
        self::$outputPath = $path;
    }

    public static function get(): ?string
    {
        return self::$outputPath;
    }

    public static function has(): bool
    {
        return self::$outputPath !== null;
    }

    public static function clear(): void
    {
        self::$outputPath = null;
    }

    public static function generate(string $path): string
    {
        // Ensure folder path ends with directory separator
        $folderPath = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return $folderPath.self::generateFilename();
    }

    private static function generateFilename(): string
    {
        /** @phpstan-ignore-next-line */
        $testName = (string) test()->name();
        $datetime = date('Y_m_d_H_i_s');

        // Remove Pest's internal prefix
        $testName = str_replace('__pest_evaluable_', '', $testName);

        // Sanitize test name for filename (convert special characters to underscores, like Pest does)
        $sanitizedTestName = (string) preg_replace('/[^a-zA-Z0-9-_]/', '_', $testName);
        $sanitizedTestName = (string) preg_replace('/_+/', '_', $sanitizedTestName); // Replace multiple underscores with single underscore
        $sanitizedTestName = trim($sanitizedTestName, '_'); // Remove leading/trailing underscores

        return $datetime.'_'.$sanitizedTestName.'.html';
    }
}
