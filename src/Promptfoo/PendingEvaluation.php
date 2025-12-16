<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Path;

/**
 * @internal
 */
final readonly class PendingEvaluation
{
    public function __construct(
        public Evaluation $evaluation,
        public string $configPath,
        public string $outputPath,
        public ?string $userOutputPath = null,
    ) {}

    public static function create(Evaluation $evaluation): self
    {
        $userOutputPath = null;
        $built = $evaluation->build();

        if (Promptfoo::shouldOutput()) {
            /** @phpstan-ignore-next-line */
            $testName = $built->description ?: test()->name();

            $userOutputPath = Path::withFileName($testName)
                ->inFolder(Promptfoo::outputFolder())
                ->withExtension('html')
                ->includeDatetime()
                ->includeUniqueId()
                ->toString();
        }

        return new self(
            evaluation: $evaluation,
            configPath: Path::withFileName('promptfoo_config')
                ->inFolder(sys_get_temp_dir())
                ->withExtension('yaml')
                ->includeUniqueId()
                ->toString(),
            outputPath: Path::withFileName('promptfoo_output')
                ->inFolder(sys_get_temp_dir())
                ->withExtension('json')
                ->includeUniqueId()
                ->toString(),
            userOutputPath: $userOutputPath,
        );
    }
}
