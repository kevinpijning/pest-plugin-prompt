<?php

declare(strict_types=1);

namespace Pest\Prompt\Promptfoo\Results;

class Provider
{
    public function __construct(
        public readonly string $id,
        public readonly string $label,
    ) {}
}
