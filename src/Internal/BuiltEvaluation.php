<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

/**
 * @internal
 */
final readonly class BuiltEvaluation
{
    /**
     * @param  string[]  $prompts
     * @param  BuiltProvider[]  $providers
     * @param  BuiltTestCase[]  $testCases
     */
    public function __construct(
        public ?string $description,
        public array $prompts,
        public array $providers,
        public array $testCases,
        public ?BuiltTestCase $defaultTestCase,
    ) {}
}
