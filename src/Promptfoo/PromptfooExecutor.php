<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

use KevinPijning\Prompt\Contracts\EvaluatorClient;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Exceptions\ExecutionException;
use KevinPijning\Prompt\Internal\EvaluationResult;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

final class PromptfooExecutor implements EvaluatorClient
{
    public function __construct(
        private readonly PromptfooConfiguration $config,
    ) {}

    public function evaluate(Evaluation $evaluation): EvaluationResult
    {
        $context = EvaluationContext::create($evaluation, $this->config);

        try {
            $this->writeConfig($context);

            $command = $this->buildCommand($context);
            $this->execute($command);

            $result = $this->parseOutput($context->outputPath);
        } finally {
            $this->cleanup($context);
        }

        return $result;
    }

    /**
     * @param  string[]  $command
     *
     * @throws ExecutionException
     */
    private function execute(array $command): void
    {
        $process = new Process($command, env: $_ENV);

        $process->setTimeout($this->config->timeout());

        try {
            $process->run();
        } catch (ProcessTimedOutException) {
            throw new ExecutionException(
                sprintf('Promptfoo command timed out after %d seconds. The process may be hanging or waiting for a response.', $this->config->timeout()),
                implode(' ', $command),
                $process->getOutput().$process->getErrorOutput(),
                $process->getExitCode() ?? 1
            );
        }

        $combinedOutput = $process->getOutput().$process->getErrorOutput();

        $evaluationComplete = str_contains($combinedOutput, 'Evaluation complete') ||
            str_contains($combinedOutput, '✔ Evaluation complete');

        if (! $process->isSuccessful() && ! $evaluationComplete) {
            throw new ExecutionException(
                sprintf(
                    'Promptfoo command failed: %s',
                    $process->getErrorOutput() ?: $process->getOutput()
                ),
                implode(' ', $command),
                $process->getOutput(),
                $process->getExitCode() ?? 1
            );
        }
    }

    /**
     * @return string[]
     */
    private function buildCommand(EvaluationContext $context): array
    {
        $command = [
            ...explode(' ', $this->config->command()), 'eval',
            '--config', $context->configPath,
            '--output', $context->outputPath,
        ];

        if ($context->userOutputPath !== null) {
            $command[] = '--output';
            $command[] = $context->userOutputPath;
        }

        return $command;
    }

    private function writeConfig(EvaluationContext $context): void
    {
        $configYaml = ConfigBuilder::fromEvaluation($context->evaluation)->toYaml();

        file_put_contents($context->configPath, $configYaml);
    }

    private function parseOutput(string $outputPath): EvaluationResult
    {
        return EvaluationResultBuilder::fromJson($outputPath);
    }

    private function cleanup(EvaluationContext $context): void
    {
        if (file_exists($context->outputPath)) {
            unlink($context->outputPath);
        }

        if (file_exists($context->configPath)) {
            unlink($context->configPath);
        }
    }
}
