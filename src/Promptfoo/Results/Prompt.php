<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo\Results;

class Prompt
{
    public function __construct(
        public readonly string $raw,
        public readonly string $label,
    ) {}
}
