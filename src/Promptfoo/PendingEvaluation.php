<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo;

use Pest\Prompt\Api\Evaluation;

class PendingEvaluation
{
    public function __construct(
        public readonly Evaluation $evaluation,
        public readonly string $configPath,
        public readonly string $outputPath,
    ) {}

    public static function create(Evaluation $evaluation): self
    {
        return new self(
            $evaluation,
            sys_get_temp_dir().'/promptfoo_config_'.uniqid().'.yaml',
            sys_get_temp_dir().'/promptfoo_output_'.uniqid().'.json',
        );
    }
}
