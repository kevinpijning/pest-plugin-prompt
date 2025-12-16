<?php

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\TestContext;
use KevinPijning\Prompt\Provider;

if (! function_exists('provider')) {
    function provider(string $name, ?callable $config = null): Provider
    {
        if (is_null($config)) {
            return TestContext::addProvider($name, new Provider);
        }

        return TestContext::addProvider($name, $config(new Provider));
    }
}

if (! function_exists('prompt')) {
    function prompt(string ...$prompts): Evaluation
    {
        return TestContext::addEvaluation(new Evaluation($prompts));
    }
}
