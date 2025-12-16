<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

final class PromptfooConfiguration
{
    /**
     * @param  string[]  $defaultProviders
     */
    public function __construct(
        private readonly string $command = 'npx promptfoo@latest',
        private readonly int $timeout = 300,
        private readonly array $defaultProviders = ['openai:gpt-4o-mini'],
        private readonly ?string $outputFolder = null,
    ) {}

    public function command(): string
    {
        return $this->command;
    }

    public function timeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return string[]
     */
    public function defaultProviders(): array
    {
        return $this->defaultProviders;
    }

    public function outputFolder(): ?string
    {
        return $this->outputFolder;
    }

    public function shouldOutput(): bool
    {
        return $this->outputFolder !== null;
    }

    public function withCommand(string $command): self
    {
        return new self(
            command: $command,
            timeout: $this->timeout,
            defaultProviders: $this->defaultProviders,
            outputFolder: $this->outputFolder,
        );
    }

    public function withTimeout(int $timeout): self
    {
        return new self(
            command: $this->command,
            timeout: $timeout,
            defaultProviders: $this->defaultProviders,
            outputFolder: $this->outputFolder,
        );
    }

    /**
     * @param  string[]  $defaultProviders
     */
    public function withDefaultProviders(array $defaultProviders): self
    {
        return new self(
            command: $this->command,
            timeout: $this->timeout,
            defaultProviders: $defaultProviders,
            outputFolder: $this->outputFolder,
        );
    }

    public function withOutputFolder(?string $outputFolder): self
    {
        return new self(
            command: $this->command,
            timeout: $this->timeout,
            defaultProviders: $this->defaultProviders,
            outputFolder: $outputFolder,
        );
    }
}
