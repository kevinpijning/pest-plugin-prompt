<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Helpers;

final class AssertionGroupName
{
    public static function fromMethodName(string $methodName): ?string
    {
        if (! str_starts_with($methodName, 'to') || strlen($methodName) <= 2) {
            return null;
        }

        $short = substr($methodName, 2);
        $withSpaces = preg_replace('/(?<!^)[A-Z]/', ' $0', $short) ?? $short;

        return strtolower(trim($withSpaces));
    }
}
