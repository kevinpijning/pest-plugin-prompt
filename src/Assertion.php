<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

class Assertion
{
    public function __construct(
        public readonly string $type,
        public readonly mixed $value = null,
        public readonly ?float $threshold = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $options = null,
    ) {}

    public function negate(): self
    {
        return new self(
            type: $this->negateType($this->type),
            value: $this->value,
            threshold: $this->threshold,
            options: $this->options,
        );
    }

    private function negateType(string $type): string
    {
        $prefix = 'not-';

        if (str_starts_with($type, $prefix)) {
            return substr($type, strlen($prefix));
        }

        return $prefix.$type;
    }
}
