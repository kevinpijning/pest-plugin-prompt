<?php

declare(strict_types=1);

namespace Pest\Prompt;

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

    public static function withHtmlFallback(string $path): string
    {
        // If path ends with slash, it's a folder - generate filename with test name and datetime
        if (str_ends_with($path, DIRECTORY_SEPARATOR)) {
            return $path.self::generateFilename();
        }

        $pathInfo = pathinfo($path);

        // If no extension, add .html (for file paths)
        if (! isset($pathInfo['extension']) || $pathInfo['extension'] === '') {
            return $path.'.html';
        }

        return $path;
    }

    private static function generateFilename(): string
    {
        $datetime = date('Y-m-d-H-i-s');

        return $datetime.'.html';
    }
}
