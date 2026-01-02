<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Exceptions;

use KevinPijning\Prompt\Helpers\SourceLocation;
use NunoMaduro\Collision\Contracts\RenderableOnCollisionEditor;
use PHPUnit\Framework\AssertionFailedError;
use Whoops\Exception\Frame;

final class PromptAssertionFailedException extends AssertionFailedError implements RenderableOnCollisionEditor
{
    public function __construct(
        private readonly SourceLocation $sourceLocation,
        string $message,
    ) {
        parent::__construct($message);
    }

    public function toCollisionEditor(): Frame
    {
        return new Frame([
            'file' => $this->sourceLocation->file,
            'line' => $this->sourceLocation->line,
        ]);
    }

    public function getSourceLocation(): SourceLocation
    {
        return $this->sourceLocation;
    }
}
