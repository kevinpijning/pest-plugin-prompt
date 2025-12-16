<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use KevinPijning\Prompt\Internal\TestLifecycle;
use KevinPijning\Prompt\Promptfoo\Promptfoo;
use KevinPijning\Prompt\Promptfoo\PromptfooExecutor;
use Pest\Contracts\Plugins\Bootable;
use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Plugins\Concerns\HandleArguments;
use Pest\TestSuite;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * @internal
 */
final class Plugin implements Bootable, HandlesArguments
{
    use HandleArguments;

    private const string OUTPUT_FLAG = '--output';

    private const string DEFAULT_OUTPUT_PATH = 'prompt-tests-output';

    public function boot(): void
    {
        pest()->afterEach(function (): void {
            TestLifecycle::evaluate();
        })->in($this->in());

        // Merge parallel caches after all tests complete
        pest()->afterAll(function (): void {
            // #region agent log
            $logData = [
                'sessionId' => 'debug-session',
                'runId' => 'post-fix-v3',
                'location' => __FILE__.':'.__LINE__,
                'message' => 'afterAll hook CALLED',
                'data' => [
                    'pid' => getmypid(),
                    'timestamp' => microtime(true),
                ],
                'timestamp' => (int) (microtime(true) * 1000),
                'hypothesisId' => 'AFTERALL',
            ];
            file_put_contents('/Users/kevinpijning/workspace/pest-plugin-prompts/.cursor/debug.log', json_encode($logData)."\n", FILE_APPEND);
            // #endregion
            
            PromptfooExecutor::mergeParallelCaches();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function handleArguments(array $arguments): array
    {
        if (! $this->hasArgument(self::OUTPUT_FLAG, $arguments)) {
            return $arguments;
        }

        $input = new ArgvInput(array_values($arguments));

        $outputPath = $input->getParameterOption(self::OUTPUT_FLAG);

        // Use default path if --output is provided without a value
        if (! is_string($outputPath) || $outputPath === '') {
            $outputPath = self::DEFAULT_OUTPUT_PATH;
        }

        Promptfoo::setOutputFolder($outputPath);

        // Remove the value if it exists as a separate argument (e.g., 'test-output' in '--output test-output')
        $arguments = $this->popArgument($outputPath, $arguments);
        // Remove --output if it exists as a separate argument (e.g., '--output' in '--output test-output')
        $arguments = $this->popArgument(self::OUTPUT_FLAG, $arguments);
        // Remove --output=path variant if it exists (e.g., '--output=test-output')
        $arguments = $this->popArgument(self::OUTPUT_FLAG.'='.$outputPath, $arguments);

        return $arguments;
    }

    private function in(): string
    {
        return TestSuite::getInstance()->rootPath.DIRECTORY_SEPARATOR.TestSuite::getInstance()->testPath;
    }
}
