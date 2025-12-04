<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Api\Evaluation;
use Pest\Plugin;

Plugin::uses(Promptable::class);

function prompt(string ...$prompts): Evaluation
{
    $evaluation = new Evaluation($prompts);

    TestContext::addEvaluation($evaluation);

    return $evaluation;
}
