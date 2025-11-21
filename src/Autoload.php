<?php

declare(strict_types=1);

namespace Pest\Prompt;

use Pest\Plugin;
use Pest\Prompt\Api\Evaluation;

Plugin::uses(Promptable::class);

function prompt(string ...$prompts): Evaluation
{
    $evaluation = new Evaluation($prompts);

    TestContext::addEvaluation($evaluation);

    return $evaluation;
}
