<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\Provider;
use KevinPijning\Prompt\Api\TestCase;
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
            'assertionTemplates' => $this->mapAssertionTemplates(),
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
            'id' => $provider->getId(),
            'label' => $provider->getLabel(),
            'config' => array_filter([
                ...array_filter($provider->getConfig()),
                'temperature' => $provider->getTemperature(),
                'top_p' => $provider->getTopP(),
                'frequency_penalty' => $provider->getFrequencyPenalty(),
                'presence_penalty' => $provider->getPresencePenalty(),
                'stop' => $provider->getStop(),
            ]),
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
        return array_map(static fn (Assertion $assertion): array => $assertion->templateName !== null
            ? ['$ref' => '#/assertionTemplates/'.$assertion->templateName]
            : array_filter([
                'type' => $assertion->type,
                'value' => $assertion->value,
                'threshold' => $assertion->threshold,
                'options' => $assertion->options,
            ]), $assertions);
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    private function mapAssertionTemplates(): array
    {
        return array_map(static fn (Assertion $assertion): array => array_filter([
            'type' => $assertion->type,
            'value' => $assertion->value,
            'threshold' => $assertion->threshold,
            'options' => $assertion->options,
        ]), $this->evaluation->assertionTemplates());
    }
}
