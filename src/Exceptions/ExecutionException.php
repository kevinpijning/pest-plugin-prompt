<?php

declare(strict_types=1);

namespace Pest\Prompt\Exceptions;

use Exception;

class ExecutionException extends Exception
{
    public function __construct(string $message, private readonly string $command, private readonly string $output = '', private readonly int $exitCode = 1)
    {
        parent::__construct($message);
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }
}
