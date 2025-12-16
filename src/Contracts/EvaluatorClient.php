<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Contracts;

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Promptfoo\EvaluationResult;

interface EvaluatorClient
{
    public function evaluate(Evaluation $evaluation): EvaluationResult;
}
