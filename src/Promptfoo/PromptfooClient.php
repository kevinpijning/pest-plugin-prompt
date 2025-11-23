<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo;

use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\Contracts\EvaluatorClient;
use Pest\Prompt\Exceptions\ExecutionException;
use Symfony\Component\Process\Process;

class PromptfooClient implements EvaluatorClient
{
    public function __construct(
        private readonly string $promptfooCommand
    ) {}

    public function evaluate(Evaluation $evaluation): EvaluationResult
    {
        $pendingEvaluation = PendingEvaluation::create($evaluation);

        try {
            $this->generateConfig($pendingEvaluation);

            $command = $this->generateCommand($pendingEvaluation);
            $this->execute($command);

            $result = $this->parseOutput($pendingEvaluation->outputPath);
        } finally {
            $this->cleanup($pendingEvaluation);
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

        $process->setTimeout(300);

        $process->run();

        if (! $process->isSuccessful()) {
            $combinedOutput = $process->getOutput().$process->getErrorOutput();
            throw new ExecutionException('Command execution failed.', implode(' ', $command), $combinedOutput, $process->getExitCode() ?: 1);
        }
    }

    /**
     * @return string[]
     */
    private function generateCommand(PendingEvaluation $pendingEvaluation): array
    {
        return [
            ...explode(' ', $this->promptfooCommand), 'eval',
            '--config', $pendingEvaluation->configPath,
            '--output', $pendingEvaluation->outputPath,
        ];
    }

    private function generateConfig(PendingEvaluation $pendingEvaluation): void
    {
        $configYaml = ConfigBuilder::fromEvaluation($pendingEvaluation->evaluation)->toYaml();

        file_put_contents($pendingEvaluation->configPath, $configYaml);
    }

    private function parseOutput(string $outputPath): EvaluationResult
    {
        return EvaluationResultBuilder::fromJson($outputPath);
    }

    private function cleanup(PendingEvaluation $pendingEvaluation): void
    {
        if (file_exists($pendingEvaluation->outputPath)) {
            unlink($pendingEvaluation->outputPath);
        }

        if (file_exists($pendingEvaluation->configPath)) {
            unlink($pendingEvaluation->configPath);
        }
    }
}
