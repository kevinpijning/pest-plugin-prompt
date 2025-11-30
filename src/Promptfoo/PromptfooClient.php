<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo;

use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\Contracts\EvaluatorClient;
use Pest\Prompt\Exceptions\ExecutionException;
use Pest\Prompt\OutputPath;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
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

        try {
            $process->run();
        } catch (ProcessTimedOutException) {
            throw new ExecutionException(
                'Promptfoo command timed out after 300 seconds. The process may be hanging or waiting for a response.',
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
    private function generateCommand(PendingEvaluation $pendingEvaluation): array
    {
        $command = [
            ...explode(' ', $this->promptfooCommand), 'eval',
            '--config', $pendingEvaluation->configPath,
            '--output', $pendingEvaluation->outputPath,
        ];

        // Add user-specified output path (promptfoo determines format by extension)
        if (! OutputPath::has()) {
            return $command;
        }

        $outputPath = OutputPath::get();

        if ($outputPath !== null) {
            $command[] = '--output';
            $command[] = OutputPath::withHtmlFallback($outputPath);
        }

        return $command;
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
