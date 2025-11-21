<?php

declare(strict_types=1);

namespace Pest\Prompt\Api;

class Evaluation
{
    private ?string $description = null;

    /** @var string[] */
    private array $prompts = [];

    /** @var string[] */
    private array $providers = [];

    /** @var TestCase[] */
    private array $testCases = [];

    public function __construct(
        array $prompts
    ) {
        $this->prompts = $prompts;
    }

    public function describe(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function usingProvider(string ...$providers): self
    {
        foreach ($providers as $provider) {
            $this->providers[] = $provider;
        }

        return $this;
    }

    /**
     * @param  array<string,mixed>  $variables
     */
    public function expect(array $variables = []): TestCase
    {
        $testCase = new TestCase($variables, $this);
        $this->testCases[] = $testCase;

        return $testCase;
    }

    public function clearTests(): self
    {
        $this->testCases = [];

        return $this;
    }
}
