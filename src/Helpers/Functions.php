<?php

use KevinPijning\Prompt\AssertionGroup;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Internal\AssertionGroupRegistry;
use KevinPijning\Prompt\Internal\EvaluationRegistry;
use KevinPijning\Prompt\Internal\ProviderRegistry;
use KevinPijning\Prompt\Provider;
use KevinPijning\Prompt\ProviderFactory;

if (! function_exists('provider')) {
    /**
     * Create or register a provider.
     *
     * When called without arguments, returns a ProviderFactory for extension registration.
     * When called with a name, registers and returns a Provider instance.
     */
    function provider(?string $name = null, ?callable $config = null): Provider|ProviderFactory
    {
        if (is_null($name)) {
            return new ProviderFactory;
        }

        if (is_null($config)) {
            return ProviderRegistry::add($name, new Provider);
        }

        return ProviderRegistry::add($name, $config(new Provider));
    }
}

if (! function_exists('assertion')) {
    /**
     * @param  callable|null  $config  Optional callback that receives the TestCase and extra parameters
     */
    function assertion(string $name, ?callable $config = null): AssertionGroup
    {
        $group = new AssertionGroup($name, $config);

        return AssertionGroupRegistry::add($name, $group);
    }
}

if (! function_exists('prompt')) {
    function prompt(string ...$prompts): Evaluation
    {
        return EvaluationRegistry::addEvaluation(new Evaluation($prompts));
    }
}
