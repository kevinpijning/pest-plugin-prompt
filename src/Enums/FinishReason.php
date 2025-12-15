<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Enums;

enum FinishReason: string
{
    case Stop = 'stop';
    case Length = 'length';
    case ContentFilter = 'content_filter';
    case ToolCalls = 'tool_calls';
}
