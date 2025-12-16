<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Internal\Results;

/**
 * @internal
 */
final readonly class Provider
{
    public function __construct(
        public string $id,
        public string $label,
    ) {}
}
