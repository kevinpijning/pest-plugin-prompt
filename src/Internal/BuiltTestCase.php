<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\Api\Assertion;

/**
 * @internal
 */
final readonly class BuiltTestCase
{
    /**
     * @param  array<string,mixed>  $variables
     * @param  Assertion[]  $assertions
     */
    public function __construct(
        public readonly array $variables,
        public readonly array $assertions,
    ) {}
}

