<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Contracts;

use KevinPijning\Prompt\Assertion;

interface AssertsPrompts
{
    public function assert(Assertion $assertion): self;
}
