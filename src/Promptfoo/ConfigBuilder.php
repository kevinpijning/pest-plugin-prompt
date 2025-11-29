<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo;

use Pest\Prompt\Api\Assertion;
use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\Api\Provider;
use Pest\Prompt\Api\TestCase;
use Symfony\Component\Yaml\Yaml;

final readonly class ConfigBuilder
{
    private function __construct(private Evaluation $evaluation) {}

    public static function fromEvaluation(Evaluation $evaluation): self
    {
        return new self($evaluation);
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'description' => $this->evaluation->description(),
            'prompts' => $this->evaluation->prompts(),
            'providers' => $this->mapProviders(),
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
        return array_map(fn (Provider $provider): array => array_filter([
            'id' => $provider->id,
        ]), $this->evaluation->providers());
    }

    /**
     * @return array<string,mixed>
     */
    private function mapTests(): array
    {
        $self = $this;

        return array_map(static fn (TestCase $testCase): array => array_filter([
            'vars' => $testCase->variables(),
            'assert' => $self->mapAssertions($testCase->assertions()),
        ]), $this->evaluation->testCases());
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
