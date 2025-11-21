<?php

declare(strict_types=1);

namespace Pest\Prompt;

use Pest\Prompt\Api\Evaluation;

/**
 * @internal
 */
trait Promptable // @phpstan-ignore-line
{
    /**
     * Example description.
     */
    public function prompt(string ...$prompts): Evaluation
    {
        return $this->prompt(...$prompts);
    }
}
