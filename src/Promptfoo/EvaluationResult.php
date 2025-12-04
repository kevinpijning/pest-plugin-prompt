<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use KevinPijning\Prompt\Promptfoo\Results\Result;

class EvaluationResult
{
    /** @param array<int, Result> $results */
    public function __construct(public readonly array $results) {}
}
