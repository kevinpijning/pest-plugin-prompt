<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api;

class Assertion
{
    public function __construct(
        public readonly string $type,
        public readonly mixed $value = null,
        public readonly ?float $threshold = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $options = null,
        public readonly ?string $templateName = null,
    ) {}

    public static function template(string $name): self
    {
        return new self(
            type: 'template-ref',
            templateName: $name,
        );
    }
}
