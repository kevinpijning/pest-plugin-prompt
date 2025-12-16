<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Helpers\Path;

/**
 * @internal
 */
final readonly class EvaluationContext
{
    public function __construct(
        public Evaluation $evaluation,
        public string $configPath,
        public string $outputPath,
        public ?string $userOutputPath = null,
    ) {}

    public static function create(Evaluation $evaluation, PromptfooConfiguration $config): self
    {
        $userOutputPath = null;
        $built = $evaluation->build();

        if ($config->shouldOutput()) {
            /** @phpstan-ignore-next-line */
            $testName = $built->description ?: test()->name();

            $userOutputPath = Path::withFileName($testName)
                ->inFolder($config->outputFolder())
                ->withExtension('html')
                ->includeDatetime()
                ->includeUniqueId()
                ->toString();
        }

        return new self(
            evaluation: $evaluation,
            configPath: self::generateTempPath('promptfoo_config', 'yaml'),
            outputPath: self::generateTempPath('promptfoo_output', 'json'),
            userOutputPath: $userOutputPath,
        );
    }

    private static function generateTempPath(string $prefix, string $extension): string
    {
        return Path::withFileName($prefix)
            ->inFolder(sys_get_temp_dir())
            ->withExtension($extension)
            ->includeUniqueId()
            ->toString();
    }
}
