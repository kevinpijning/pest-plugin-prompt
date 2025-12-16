<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal\Results;

/**
 * @internal
 */
final readonly class TestCase
{
    /**
     * @param  array<string, mixed>  $vars
     * @param  array<int, array<string, mixed>>  $assert
     * @param  array<string, mixed>  $options
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public array $vars,
        public array $assert,
        public array $options,
        public array $metadata,
    ) {}
}
