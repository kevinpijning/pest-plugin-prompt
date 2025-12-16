<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Internal\BuiltEvaluation;
use KevinPijning\Prompt\Internal\BuiltProvider;
use KevinPijning\Prompt\Internal\BuiltTestCase;
use KevinPijning\Prompt\Promptfoo\Promptfoo;
use KevinPijning\Prompt\TestContext;

class Evaluation
{
    private ?string $description = null;

    /** @var Provider[] */
    private array $providers = [];

    /** @var TestCase[] */
    private array $testCases = [];

    private ?TestCase $defaultTestCase = null;

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
     * @param  callable(TestCase): void|null  $callback
     */
    public function expect(array $variables = [], ?callable $callback = null): TestCase
    {
        $testCase = new TestCase($variables, $this);
        $this->testCases[] = $testCase;

        if (is_callable($callback)) {
            $testCase->to($callback);
        }

        return $testCase;
    }

    /**
     * @param  array<string,mixed>  $defaultVariables
     * @param  callable(TestCase): void|null  $callback
     */
    public function alwaysExpect(array $defaultVariables = [], ?callable $callback = null): TestCase
    {
        if (! $this->defaultTestCase instanceof TestCase) {
            $this->defaultTestCase = new TestCase($defaultVariables, $this);
        }

        if (is_callable($callback)) {
            $this->defaultTestCase->to($callback);
        }

        return $this->defaultTestCase;
    }

    public function build(): BuiltEvaluation
    {
        $builtProviders = array_map(
            fn (Provider $provider): BuiltProvider => $provider->build(),
            $this->providers
        );

        $builtTestCases = array_map(
            fn (TestCase $testCase): BuiltTestCase => $testCase->build(),
            $this->testCases
        );

        $builtDefaultTestCase = $this->defaultTestCase?->build();

        return new BuiltEvaluation(
            description: $this->description,
            prompts: $this->prompts,
            providers: $builtProviders,
            testCases: $builtTestCases,
            defaultTestCase: $builtDefaultTestCase,
        );
    }
}
