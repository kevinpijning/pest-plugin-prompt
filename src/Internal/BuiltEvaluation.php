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
        public readonly ?string $description,
        public readonly array $prompts,
        public readonly array $providers,
        public readonly array $testCases,
        public readonly ?BuiltTestCase $defaultTestCase,
    ) {}
}

