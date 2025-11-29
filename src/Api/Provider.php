<?php

declare(strict_types=1);

namespace Pest\Prompt\Api;

class Provider
{
    public function __construct(public readonly string $id) {}

    public static function id(string $id): self
    {
        return new self($id);
    }
}
