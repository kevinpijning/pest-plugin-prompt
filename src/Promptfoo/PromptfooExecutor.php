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
    private static array $parallelCachePaths = [];

    public function __construct(
        private readonly PromptfooConfiguration $config,
    ) {}

    public function evaluate(Evaluation $evaluation): EvaluationResult
    {
        // Lazy merge: if we're not in parallel mode and parallel caches exist, merge them now
        // This handles the case where afterAll doesn't fire in parallel mode
        if (! $this->isRunningInParallel()) {
            self::mergeParallelCaches();
        }

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
        // Detect if we're running in parallel mode
        $isParallel = $this->isRunningInParallel();

        $env = $_ENV;
        $processId = getmypid();
        $defaultCachePath = (getenv('HOME') ?: sys_get_temp_dir()).'/.promptfoo/cache';

        if ($isParallel) {
            // In parallel mode: use process-specific cache to prevent overwrites
            // We'll merge these into the main cache after all tests complete
            $processCachePath = $defaultCachePath.'_parallel_'.$processId;
            $env['PROMPTFOO_CACHE_PATH'] = $processCachePath;

            // Ensure the process-specific cache directory exists
            if (! is_dir($processCachePath)) {
                @mkdir($processCachePath, 0755, true);
            }

            // Register this cache path for later merging
            self::registerParallelCachePath($processCachePath);
        } else {
            // In sequential mode: use predictable default cache path
            // Don't set PROMPTFOO_CACHE_PATH, let promptfoo use default ~/.promptfoo/cache
            // This ensures cache is predictable and reusable across runs
        }

        $process = new Process($command, env: $env);
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
     * Detect if tests are running in parallel mode.
     */
    private function isRunningInParallel(): bool
    {
        // Check for PEST_PARALLEL environment variable (set by Pest when running in parallel)
        if (getenv('PEST_PARALLEL') !== false) {
            return true;
        }

        // Check if --parallel flag is in command line arguments
        if (in_array('--parallel', $_SERVER['argv'] ?? [], true)) {
            return true;
        }

        // Check for PARATEST environment variable (used by some parallel test runners)
        if (getenv('PARATEST') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Register a parallel cache path for later merging.
     */
    private static function registerParallelCachePath(string $cachePath): void
    {
        if (! in_array($cachePath, self::$parallelCachePaths, true)) {
            self::$parallelCachePaths[] = $cachePath;
        }
    }

    /**
     * Merge all parallel caches into the main cache.
     * This should be called after all parallel tests complete.
     */
    public static function mergeParallelCaches(): void
    {
        $defaultCachePath = (getenv('HOME') ?: sys_get_temp_dir()).'/.promptfoo/cache';
        $mainCacheFile = $defaultCachePath.'/cache.json';

        // Find all parallel cache directories (not just the ones registered in this process)
        // This is necessary because in parallel mode, each process has its own static array
        $parallelCacheDirs = [];
        $baseDir = dirname($defaultCachePath);
        $cacheDirName = basename($defaultCachePath);

        if (is_dir($baseDir)) {
            $entries = @scandir($baseDir);
            if ($entries !== false) {
                foreach ($entries as $entry) {
                    if ($entry === '.' || $entry === '..') {
                        continue;
                    }

                    $fullPath = $baseDir.'/'.$entry;
                    if (is_dir($fullPath) && str_starts_with($entry, $cacheDirName.'_parallel_')) {
                        $parallelCacheDirs[] = $fullPath;
                    }
                }
            }
        }

        // Also include any registered paths (for this process)
        $parallelCacheDirs = array_unique(array_merge($parallelCacheDirs, self::$parallelCachePaths));

        if (empty($parallelCacheDirs)) {
            return;
        }

        // Load main cache if it exists
        $mainCache = [];
        if (file_exists($mainCacheFile)) {
            $mainCacheData = json_decode(file_get_contents($mainCacheFile), true);
            $mainCache = $mainCacheData['cache'] ?? [];
        }

        $mergedCount = 0;
        $totalEntriesBefore = count($mainCache);

        // Merge all parallel caches
        foreach ($parallelCacheDirs as $parallelCachePath) {
            $parallelCacheFile = $parallelCachePath.'/cache.json';

            if (! file_exists($parallelCacheFile)) {
                continue;
            }

            $parallelCacheData = json_decode(file_get_contents($parallelCacheFile), true);
            $parallelCache = $parallelCacheData['cache'] ?? [];

            // Merge entries: use the one with the latest expire time for duplicates
            foreach ($parallelCache as $entry) {
                if (! is_array($entry) || count($entry) < 2) {
                    continue;
                }

                $key = $entry[0] ?? null;
                $value = $entry[1] ?? null;

                if ($key === null || $value === null) {
                    continue;
                }

                // Check if key already exists in main cache
                $existingIndex = null;
                foreach ($mainCache as $index => $mainEntry) {
                    if (is_array($mainEntry) && ($mainEntry[0] ?? null) === $key) {
                        $existingIndex = $index;
                        break;
                    }
                }

                if ($existingIndex !== null) {
                    // Keep the entry with the latest expire time
                    $existingExpire = $mainCache[$existingIndex][1]['expire'] ?? 0;
                    $newExpire = $value['expire'] ?? 0;

                    if ($newExpire > $existingExpire) {
                        $mainCache[$existingIndex] = $entry;
                        $mergedCount++;
                    }
                } else {
                    // Add new entry
                    $mainCache[] = $entry;
                    $mergedCount++;
                }
            }

            // Clean up parallel cache directory
            @unlink($parallelCacheFile);
            @rmdir($parallelCachePath);
        }

        // Save merged cache
        if (! is_dir($defaultCachePath)) {
            @mkdir($defaultCachePath, 0755, true);
        }

        $mergedCacheData = ['cache' => $mainCache];
        if (file_exists($mainCacheFile)) {
            $existingData = json_decode(file_get_contents($mainCacheFile), true);
            // Preserve other cache metadata (like lastExpire)
            if (isset($existingData['lastExpire'])) {
                $mergedCacheData['lastExpire'] = $existingData['lastExpire'];
            }
            // Update lastExpire to the latest expire time from merged entries
            $latestExpire = 0;
            foreach ($mainCache as $entry) {
                if (is_array($entry) && isset($entry[1]['expire'])) {
                    $latestExpire = max($latestExpire, $entry[1]['expire']);
                }
            }
            if ($latestExpire > 0) {
                $mergedCacheData['lastExpire'] = $latestExpire;
            }
        }

        file_put_contents($mainCacheFile, json_encode($mergedCacheData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // Clear registered paths
        self::$parallelCachePaths = [];
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
