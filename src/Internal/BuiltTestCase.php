<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal;

use KevinPijning\Prompt\Assertion;

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
        public array $variables,
        public array $assertions,
    ) {}
}
