<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo;

use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\OutputPath;

class PendingEvaluation
{
    public function __construct(
        public readonly Evaluation $evaluation,
        public readonly string $configPath,
        public readonly string $outputPath,
        public readonly ?string $userOutputPath = null,
    ) {}

    public static function create(Evaluation $evaluation): self
    {
        $userOutputPath = null;

        if (OutputPath::has() && ($path = OutputPath::get()) !== null) {
            $userOutputPath = OutputPath::generate($path);
        }

        return new self(
            evaluation: $evaluation,
            configPath: sys_get_temp_dir().'/promptfoo_config_'.uniqid().'.yaml',
            outputPath: sys_get_temp_dir().'/promptfoo_output_'.uniqid().'.json',
            userOutputPath: $userOutputPath,
        );
    }
}
