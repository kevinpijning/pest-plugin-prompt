<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api;

use KevinPijning\Prompt\Promptfoo\Promptfoo;
use KevinPijning\Prompt\TestContext;

class Evaluation
{
    private ?string $description = null;

    /** @var Provider[] */
    private array $providers = [];

    /** @var TestCase[] */
    private array $testCases = [];

    public function __construct(
        /** @var string[] */
        private readonly array $prompts
    ) {}

    public function describe(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    private function addProvider(string|Provider $provider): self
    {
        if (is_string($provider)) {
            $provider = Provider::create($provider);
        }

        $this->providers[] = $provider;

        return $this;
    }

    public function usingProvider(string|Provider|callable ...$providers): self
    {
        foreach ($providers as $provider) {
            if ($provider instanceof Provider) {
                $this->addProvider($provider);

                continue;
            }

            if (is_callable($provider)) {
                $this->addProvider($provider(new Provider));

                continue;
            }

            if (TestContext::hasProvider($provider)) {
                $this->addProvider(TestContext::getProvider($provider));

                continue;
            }

            $this->addProvider($provider);
        }

        if ($providers === []) {
            return $this->addProvider(...Promptfoo::defaultProviders());
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

    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function prompts(): array
    {
        return $this->prompts;
    }

    /**
     * @return Provider[]
     */
    public function providers(): array
    {
        return $this->providers;
    }

    /**
     * @return TestCase[]
     */
    public function testCases(): array
    {
        return $this->testCases;
    }
}
