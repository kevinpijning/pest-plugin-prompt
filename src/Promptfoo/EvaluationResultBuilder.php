<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use InvalidArgumentException;
use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Promptfoo\Results\ComponentResult;
use KevinPijning\Prompt\Promptfoo\Results\GradingResult;
use KevinPijning\Prompt\Promptfoo\Results\Prompt;
use KevinPijning\Prompt\Promptfoo\Results\Provider;
use KevinPijning\Prompt\Promptfoo\Results\Response;
use KevinPijning\Prompt\Promptfoo\Results\Result;
use KevinPijning\Prompt\Promptfoo\Results\TestCase;

class EvaluationResultBuilder
{
    /** @param array<string, mixed> $data */
    public function __construct(private readonly array $data) {}

    public function build(): EvaluationResult
    {
        if (! isset($this->data['results'])) {
            throw new InvalidArgumentException('Missing required key "results" in data array');
        }

        if (! array_key_exists('results', $this->data['results'])) {
            throw new InvalidArgumentException('Missing required key "results.results" in data array');
        }

        if ($this->data['results']['results'] === null) {
            throw new InvalidArgumentException('Key "results.results" cannot be null');
        }

        if (! is_array($this->data['results']['results'])) {
            throw new InvalidArgumentException('Key "results.results" must be an array, got: '.gettype($this->data['results']['results']));
        }

        $results = [];
        foreach ($this->data['results']['results'] as $resultData) {
            $results[] = $this->buildResult($resultData);
        }

        return new EvaluationResult($results);
    }

    /** @param array<string, mixed> $data */
    private function buildResult(array $data): Result
    {
        return new Result(
            cost: (float) $data['cost'],
            error: $data['error'] ?? null,
            gradingResult: $data['gradingResult'] !== null ? $this->buildGradingResult($data['gradingResult']) : null,
            id: $data['id'],
            latencyMs: (int) $data['latencyMs'],
            namedScores: $data['namedScores'] ?? [],
            prompt: $this->buildPrompt($data['prompt']),
            promptId: $data['promptId'],
            promptIdx: (int) $data['promptIdx'],
            provider: $this->buildProvider($data['provider']),
            response: isset($data['response']) ? $this->buildResponse($data['response']) : null,
            score: (float) $data['score'],
            success: $this->castToBool($data['success']),
            testCase: $this->buildTestCase($data['testCase']),
            testIdx: (int) $data['testIdx'],
            vars: $data['vars'] ?? [],
            metadata: $data['metadata'] ?? [],
            failureReason: $data['failureReason'] ?? null,
        );
    }

    /** @param array<string, mixed> $data */
    private function buildGradingResult(array $data): GradingResult
    {
        $componentResults = [];
        foreach ($data['componentResults'] ?? [] as $componentResultData) {
            $componentResults[] = $this->buildComponentResult($componentResultData);
        }

        return new GradingResult(
            pass: $this->castToBool($data['pass']),
            score: (float) $data['score'],
            reason: $data['reason'] ?? '',
            namedScores: $data['namedScores'] ?? [],
            tokensUsed: $data['tokensUsed'] ?? [],
            componentResults: $componentResults,
        );
    }

    /** @param array<string, mixed> $data */
    private function buildComponentResult(array $data): ComponentResult
    {
        return new ComponentResult(
            pass: $this->castToBool($data['pass']),
            score: (float) $data['score'],
            reason: $data['reason'] ?? '',
            assertion: $this->buildAssertion($data['assertion']),
        );
    }

    /** @param array<string, mixed> $data */
    private function buildAssertion(array $data): Assertion
    {
        return new Assertion(
            type: $data['type'],
            value: $data['value'],
            threshold: $data['threshold'] ?? null,
            options: $data['options'] ?? null,
        );
    }

    /** @param array<string, mixed> $data */
    private function buildPrompt(array $data): Prompt
    {
        return new Prompt(
            raw: $data['raw'],
            label: $data['label'],
        );
    }

    /** @param array<string, mixed> $data */
    private function buildProvider(array $data): Provider
    {
        return new Provider(
            id: $data['id'],
            label: $data['label'] ?? '',
        );
    }

    /** @param array<string, mixed> $data */
    private function buildResponse(array $data): ?Response
    {
        if (isset($data['error'])) {
            return null;
        }

        return new Response(
            output: $data['output'],
            tokenUsage: $data['tokenUsage'] ?? [],
            cached: $this->castToBool($data['cached'] ?? false),
            latencyMs: (int) ($data['latencyMs'] ?? 0),
            finishReason: $data['finishReason'] ?? '',
            cost: (float) ($data['cost'] ?? 0.0),
            guardrails: $data['guardrails'] ?? [],
        );
    }

    /** @param array<string, mixed> $data */
    private function buildTestCase(array $data): TestCase
    {
        return new TestCase(
            vars: $data['vars'] ?? [],
            assert: $data['assert'] ?? [],
            options: $data['options'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }

    private function castToBool(mixed $value): bool
    {
        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }

    public static function fromJson(string $filePath): EvaluationResult
    {
        $contents = @file_get_contents($filePath);
        if ($contents === false) {
            throw new InvalidArgumentException("Failed to read file: {$filePath}");
        }

        return (new self(json_decode($contents, true, JSON_THROW_ON_ERROR)))->build();
    }
}
