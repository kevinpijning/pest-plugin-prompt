<?php

namespace KevinPijning\Prompt\Contracts;

use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Promptfoo\EvaluationResult;

interface EvaluatorClient
{
    public function evaluate(Evaluation $evaluation): EvaluationResult;
}
