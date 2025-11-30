<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo;

use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\OutputPath;

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

        if (Promptfoo::shouldOutput()) {
            $userOutputPath = OutputPath::from(Promptfoo::outputFolder())->generate();
        }

        return new self(
            evaluation: $evaluation,
            configPath: sys_get_temp_dir().'/promptfoo_config_'.uniqid().'.yaml',
            outputPath: sys_get_temp_dir().'/promptfoo_output_'.uniqid().'.json',
            userOutputPath: $userOutputPath,
        );
    }
}
