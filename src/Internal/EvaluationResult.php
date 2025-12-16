<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\Internal\Results\Result;

/**
 * @internal
 */
final readonly class EvaluationResult
{
    /** @param array<int, Result> $results */
    public function __construct(public array $results) {}
}
