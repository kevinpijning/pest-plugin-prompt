<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Helpers;

final readonly class SourceLocation
{
    public function __construct(
        public string $file,
        public int $line,
    ) {}

    public static function capture(): ?self
    {
        return self::fromBacktrace(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
    }

    /**
     * @param  array<int, array{file?: string, line?: int, ...}>  $backtrace
     */
    public static function fromBacktrace(array $backtrace): ?self
    {
        foreach ($backtrace as $frame) {
            if (! isset($frame['file'], $frame['line'])) {
                continue;
            }

            if (self::isPluginSourceFrame($frame['file'])) {
                continue;
            }

            if (str_contains($frame['file'], DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR)) {
                continue;
            }

            return new self($frame['file'], $frame['line']);
        }

        return null;
    }

    private static function isPluginSourceFrame(string $file): bool
    {
        return str_contains($file, 'pest-plugin-prompt'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR);
    }

    public function isValid(): bool
    {
        return $this->file !== '' && $this->line > 0;
    }
}
