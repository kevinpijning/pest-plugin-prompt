<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Concerns\CanUseAssertions;
use KevinPijning\Prompt\Concerns\CollectsAssertions;
use KevinPijning\Prompt\Contracts\AssertsPrompts;
use KevinPijning\Prompt\Internal\BuiltTestCase;

/**
 * @property-read TestCase $not
 */
class TestCase implements AssertsPrompts
{
    use CanUseAssertions;
    use CollectsAssertions;

    /**
     * @param  array<string,mixed>  $variables
     */
    public function __construct(
        private readonly array $variables,
        private readonly Evaluation $evaluation,
    ) {}

    /**
     * @param  array<string,mixed>  $variables
     * @param  callable(TestCase): void|null  $callback
     */
    public function expect(array $variables, ?callable $callback = null): self
    {
        return $this->evaluation->expect($variables, $callback);
    }

    /**
     * @param  array<string,mixed>  $variables
     * @param  callable(TestCase): void|null  $callback
     */
    public function and(array $variables, ?callable $callback = null): self
    {
        return $this->expect($variables, $callback);
    }

    public function build(): BuiltTestCase
    {
        return new BuiltTestCase(
            variables: $this->variables,
            assertions: $this->assertions,
        );
    }
}
