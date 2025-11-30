<?php

declare(strict_types=1);

namespace Pest\Prompt;

use InvalidArgumentException;
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

    private const string OUTPUT_OPTION = 'output';

    public function boot(): void
    {
        pest()->afterEach(function (): void {
            TestLifecycle::evaluate();
        })->in($this->in());
    }

    /**
     * {@inheritDoc}
     */
    public function handleArguments(array $arguments): array
    {
        if (! $this->hasArgument('--'.self::OUTPUT_OPTION, $arguments)) {
            return $arguments;
        }

        $input = new ArgvInput(array_values($arguments));

        $outputPath = $input->getParameterOption('--'.self::OUTPUT_OPTION);

        if (! is_string($outputPath) || $outputPath === '') {
            throw new InvalidArgumentException('The --output option requires a value. Example: --output="path/to/results"');
        }

        OutputPath::set($outputPath);

        // Remove the value if it exists as a separate argument (e.g., 'test-output' in '--output test-output')
        $arguments = $this->popArgument($outputPath, $arguments);
        // Remove --output if it exists as a separate argument (e.g., '--output' in '--output test-output')
        $arguments = $this->popArgument('--'.self::OUTPUT_OPTION, $arguments);
        // Remove --output=path variant if it exists (e.g., '--output=test-output')
        $arguments = $this->popArgument('--'.self::OUTPUT_OPTION.'='.$outputPath, $arguments);

        return $arguments;
    }

    private function in(): string
    {
        return TestSuite::getInstance()->rootPath.DIRECTORY_SEPARATOR.TestSuite::getInstance()->testPath;
    }
}
