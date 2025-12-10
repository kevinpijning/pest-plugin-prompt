<?php

use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\Provider;
use KevinPijning\Prompt\AssertionTemplates;
use KevinPijning\Prompt\TestContext;

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
        $evaluation = TestContext::addEvaluation(new Evaluation($prompts));
        AssertionTemplates::apply($evaluation);

        return $evaluation;
    }
}
