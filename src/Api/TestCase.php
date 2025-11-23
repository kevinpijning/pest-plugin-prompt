<?php

declare(strict_types=1);

namespace Pest\Prompt\Api;

/**
 * @mixin Assertion
 */
class TestCase
{
    /** @var Assertion[] */
    private array $assertions = [];

    /**
     * @param  array<string,mixed>  $variables
     */
    public function __construct(
        private readonly array $variables,
        private readonly Evaluation $evaluation,
    ) {}

    /**
     * @return Assertion[]
     */
    public function assertions(): array
    {
        return $this->assertions;
    }

    /**
     * @return array<string,mixed>
     */
    public function variables(): array
    {
        return $this->variables;
    }

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
            $strict ? 'contains' : 'icontains',
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
