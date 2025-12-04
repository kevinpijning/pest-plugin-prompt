<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo\Results;

class TestCase
{
    /**
     * @param  array<string, mixed>  $vars
     * @param  array<int, array<string, mixed>>  $assert
     * @param  array<string, mixed>  $options
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly array $vars,
        public readonly array $assert,
        public readonly array $options,
        public readonly array $metadata,
    ) {}
}
