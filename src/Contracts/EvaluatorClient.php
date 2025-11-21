<?php

namespace Pest\Prompt\Contracts;

use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\Promptfoo\EvaluationResult;

interface EvaluatorClient
{
    public function evaluate(Evaluation $evaluationBuilder): EvaluationResult;
}
