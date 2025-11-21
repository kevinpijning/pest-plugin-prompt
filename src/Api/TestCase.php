<?php

declare(strict_types=1);

namespace Pest\Prompt\Api;

use Pest\Prompt\Promptfoo\Assertion;

/**
 * @mixin Assertion
 */
class TestCase
{
    /** @var Assertion[] */
    private array $assertions = [];

    public function __construct(
        public readonly array $variables,
        private readonly Evaluation $evaluation,
    ) {}

    public function assert(Assertion $assertion): self
    {
        $this->assertions[] = $assertion;

        return $this;
    }

    /**
     * @param  array<string,mixed>  $options
     */
    public function toContain(string $contains, bool $strict = false, ?float $threshold = null, array $options = []): self
    {
        return $this->assert(new Assertion(
            $strict ? 'contains' : 'icontain',
            $contains,
            $threshold,
            $options,
        ));
    }

    /**
     * @param  array<string,mixed>  $variables
     */
    public function and(array $variables): self
    {
        return $this->evaluation->expect($variables);
    }
}
