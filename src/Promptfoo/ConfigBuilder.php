<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Internal\BuiltEvaluation;
use KevinPijning\Prompt\Internal\BuiltProvider;
use KevinPijning\Prompt\Internal\BuiltTestCase;
use Symfony\Component\Yaml\Yaml;

final readonly class ConfigBuilder
{
    private function __construct(private BuiltEvaluation $evaluation) {}

    public static function fromEvaluation(Evaluation $evaluation): self
    {
        return new self($evaluation->build());
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'description' => $this->evaluation->description,
            'prompts' => $this->evaluation->prompts,
            'providers' => $this->mapProviders(),
            'defaultTest' => $this->mapDefaultTest(),
            'tests' => $this->mapTests(),
        ]);
    }

    public function toYaml(): string
    {
        return Yaml::dump($this->toArray(), 5);
    }

    /**
     * @return array<array{id?: non-falsy-string}>
     */
    private function mapProviders(): array
    {
        return array_map(fn (BuiltProvider $provider): array => array_filter([
            'id' => $provider->id,
            'label' => $provider->label,
            'config' => array_filter([
                ...array_filter($provider->config),
                'temperature' => $provider->temperature,
                'max_tokens' => $provider->maxTokens,
                'top_p' => $provider->topP,
                'frequency_penalty' => $provider->frequencyPenalty,
                'presence_penalty' => $provider->presencePenalty,
                'stop' => $provider->stop,
            ]),
        ]), $this->evaluation->providers);
    }

    /**
     * @return array<string,mixed>
     */
    private function mapTests(): array
    {
        $self = $this;

        return array_map(static fn (BuiltTestCase $testCase): array => array_filter([
            'vars' => $testCase->variables,
            'assert' => $self->mapAssertions($testCase->assertions),
        ]), $this->evaluation->testCases);
    }

    /**
     * @return array<string,mixed>|null
     */
    private function mapDefaultTest(): ?array
    {
        $defaultTestCase = $this->evaluation->defaultTestCase;

        if (! $defaultTestCase instanceof BuiltTestCase) {
            return null;
        }

        return array_filter([
            'vars' => $defaultTestCase->variables,
            'assert' => $this->mapAssertions($defaultTestCase->assertions),
        ]);
    }

    /**
     * @param  Assertion[]  $assertions
     * @return array<int,array<string,mixed>>
     */
    private function mapAssertions(array $assertions): array
    {
        return array_map(static fn (Assertion $assertion): array => array_filter([
            'type' => $assertion->type,
            'value' => $assertion->value,
            'threshold' => $assertion->threshold,
            'options' => $assertion->options,
        ]), $assertions);
    }
}
