<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Api\Provider;
use KevinPijning\Prompt\Api\TestCase;
use KevinPijning\Prompt\TestContext;

test('it can be instantiated with prompts', function () {
    $prompts = ['prompt1', 'prompt2', 'prompt3'];
    $evaluation = new Evaluation($prompts);

    expect($evaluation)->toBeInstanceOf(Evaluation::class);
});

test('it can be instantiated with empty prompts array', function () {
    $evaluation = new Evaluation([]);

    expect($evaluation)->toBeInstanceOf(Evaluation::class);
});

test('describe method sets description and returns self', function () {
    $evaluation = new Evaluation(['prompt1']);
    $description = 'Test description';

    $result = $evaluation->describe($description);

    expect($result)->toBe($evaluation);
});

test('describe method can be chained', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->describe('First description')
        ->describe('Second description');

    expect($result)->toBe($evaluation);
});

test('usingProvider method adds a single provider and returns self', function () {
    $evaluation = new Evaluation(['prompt1']);
    $provider = 'openai:gpt-4';

    $result = $evaluation->usingProvider($provider);

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(1)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4');
});

test('usingProvider method can add multiple providers', function () {
    $evaluation = new Evaluation(['prompt1']);
    $provider1 = 'openai:gpt-4';
    $provider2 = 'anthropic:claude-3';
    $provider3 = 'google:gemini';

    $result = $evaluation->usingProvider($provider1, $provider2, $provider3);

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(3)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4');
});

test('usingProvider method can be called multiple times', function () {
    $evaluation = new Evaluation(['prompt1']);

    $evaluation->usingProvider('openai:gpt-4');
    $result = $evaluation->usingProvider('anthropic:claude-3');

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(2)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4');
});

test('usingProvider method can be chained', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->usingProvider('openai:gpt-4')
        ->usingProvider('anthropic:claude-3')
        ->usingProvider('google:gemini');

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(3);
});

test('usingProvider method can accept a Provider class', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider(Provider::create('openai:gpt-4o-mini'));

    expect($result->build()->providers)->toHaveCount(1)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4o-mini');
});

test('usingProvider method with empty array uses default providers', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider();

    // Should use default providers from Promptfoo
    expect($result->build()->providers)->not->toBeEmpty();
});

test('expect method creates and returns a TestCase', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];

    $testCase = $evaluation->expect($variables);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe($variables);
});

test('expect method can be called with no parameters', function () {
    $evaluation = new Evaluation(['prompt1']);

    $testCase = $evaluation->expect();

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe([]);
});

test('expect method can be called with empty variables array', function () {
    $evaluation = new Evaluation(['prompt1']);

    $testCase = $evaluation->expect([]);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe([]);
});

test('expect method can create multiple test cases', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables1 = ['key1' => 'value1'];
    $variables2 = ['key2' => 'value2'];
    $variables3 = ['key3' => 'value3'];

    $testCase1 = $evaluation->expect($variables1);
    $testCase2 = $evaluation->expect($variables2);
    $testCase3 = $evaluation->expect($variables3);

    expect($testCase1)->toBeInstanceOf(TestCase::class)
        ->and($testCase2)->toBeInstanceOf(TestCase::class)
        ->and($testCase3)->toBeInstanceOf(TestCase::class)
        ->and($testCase1)->not->toBe($testCase2)
        ->and($testCase2)->not->toBe($testCase3)
        ->and($testCase1->build()->variables)->toBe($variables1)
        ->and($testCase2->build()->variables)->toBe($variables2)
        ->and($testCase3->build()->variables)->toBe($variables3);
});

test('methods can be chained together', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation
        ->describe('Test description')
        ->usingProvider('openai:gpt-4')
        ->expect(['key' => 'value'])
        ->toContain('test');

    expect($result)->toBeInstanceOf(TestCase::class);
});

test('usingProvider method can accept a callable that configures a provider', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider(function (Provider $provider) {
        return $provider->id('openai:gpt-4')
            ->label('Custom Label')
            ->temperature(0.7);
    });

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(1)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4')
        ->and($result->build()->providers[0]->label)->toBe('Custom Label')
        ->and($result->build()->providers[0]->temperature)->toBe(0.7);
});

test('usingProvider method can accept multiple callables', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider(
        fn (Provider $p) => $p->id('openai:gpt-4'),
        fn (Provider $p) => $p->id('anthropic:claude-3')
    );

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(2)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4')
        ->and($result->build()->providers[1]->id)->toBe('anthropic:claude-3');
});

test('usingProvider method can use global provider from TestContext', function () {
    TestContext::clear();
    $evaluation = new Evaluation(['prompt1']);

    $globalProvider = Provider::create('openai:gpt-4')
        ->label('Global Provider')
        ->temperature(0.8);
    TestContext::addProvider('my-global-provider', $globalProvider);

    $result = $evaluation->usingProvider('my-global-provider');

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(1)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4')
        ->and($result->build()->providers[0]->label)->toBe('Global Provider')
        ->and($result->build()->providers[0]->temperature)->toBe(0.8);
});

test('usingProvider method can mix global providers, callables, and direct providers', function () {
    TestContext::clear();
    $evaluation = new Evaluation(['prompt1']);

    $globalProvider = Provider::create('openai:gpt-4');
    TestContext::addProvider('global', $globalProvider);

    $directProvider = Provider::create('anthropic:claude-3');

    $result = $evaluation->usingProvider(
        'global',
        fn (Provider $p) => $p->id('google:gemini'),
        $directProvider
    );

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(3)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4')
        ->and($result->build()->providers[1]->id)->toBe('google:gemini')
        ->and($result->build()->providers[2]->id)->toBe('anthropic:claude-3');
});

test('usingProvider method treats string as provider ID when not found in TestContext', function () {
    TestContext::clear();
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->usingProvider('openai:gpt-4o-mini');

    expect($result)->toBe($evaluation)
        ->and($result->build()->providers)->toHaveCount(1)
        ->and($result->build()->providers[0]->id)->toBe('openai:gpt-4o-mini');
});

test('alwaysExpect method creates and returns a TestCase', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1', 'key2' => 'value2'];

    $testCase = $evaluation->alwaysExpect($variables);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe($variables);
});

test('alwaysExpect method can be called with no parameters', function () {
    $evaluation = new Evaluation(['prompt1']);

    $testCase = $evaluation->alwaysExpect();

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe([]);
});

test('alwaysExpect method can be called with empty variables array', function () {
    $evaluation = new Evaluation(['prompt1']);

    $testCase = $evaluation->alwaysExpect([]);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe([]);
});

test('alwaysExpect method returns the same TestCase instance on subsequent calls', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];

    $testCase1 = $evaluation->alwaysExpect($variables);
    $testCase2 = $evaluation->alwaysExpect(['key2' => 'value2']);

    expect($testCase1)->toBe($testCase2)
        ->and($testCase1->build()->variables)->toBe($variables);
});

test('alwaysExpect method can be chained with assertion methods', function () {
    $evaluation = new Evaluation(['prompt1']);

    $result = $evaluation->alwaysExpect(['default' => 'value'])
        ->toBeJudged('the language is always a friendly variant')
        ->toBeJudged('the source and output language are always mentioned');

    expect($result)->toBeInstanceOf(TestCase::class)
        ->and($result->build()->assertions)->toHaveCount(2);
});

test('default test case is NOT added to testCases array', function () {
    $evaluation = new Evaluation(['prompt1']);

    $defaultTestCase = $evaluation->alwaysExpect(['default' => 'value']);
    $regularTestCase = $evaluation->expect(['key' => 'value']);

    $built = $evaluation->build();
    expect($built->testCases)->toHaveCount(1)
        ->and($built->testCases[0]->variables)->toBe(['key' => 'value'])
        ->and($built->defaultTestCase)->not->toBeNull()
        ->and($built->defaultTestCase->variables)->toBe(['default' => 'value']);
});

test('defaultTestCase getter returns the stored test case', function () {
    $evaluation = new Evaluation(['prompt1']);

    expect($evaluation->build()->defaultTestCase)->toBeNull();

    $defaultTestCase = $evaluation->alwaysExpect(['key' => 'value']);

    expect($evaluation->build()->defaultTestCase)->not->toBeNull()
        ->and($evaluation->build()->defaultTestCase->variables)->toBe(['key' => 'value']);
});

test('expect method can accept a callback that receives the test case', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];
    $callbackExecuted = false;
    $receivedTestCase = null;

    $testCase = $evaluation->expect($variables, function (TestCase $tc) use (&$callbackExecuted, &$receivedTestCase) {
        $callbackExecuted = true;
        $receivedTestCase = $tc;
    });

    expect($callbackExecuted)->toBeTrue()
        ->and($receivedTestCase)->toBe($testCase)
        ->and($testCase->build()->variables)->toBe($variables);
});

test('expect method callback can be used to add assertions', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];

    $testCase = $evaluation->expect($variables, function (TestCase $tc) {
        $tc->toContain('test')
            ->toContain('value');
    });

    expect($testCase->build()->assertions)->toHaveCount(2);
});

test('expect method works without callback', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];

    $testCase = $evaluation->expect($variables);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe($variables);
});

test('expect method callback can be null', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];

    $testCase = $evaluation->expect($variables, null);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe($variables);
});

test('alwaysExpect method can accept a callback that receives the test case', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];
    $callbackExecuted = false;
    $receivedTestCase = null;

    $testCase = $evaluation->alwaysExpect($variables, function (TestCase $tc) use (&$callbackExecuted, &$receivedTestCase) {
        $callbackExecuted = true;
        $receivedTestCase = $tc;
    });

    expect($callbackExecuted)->toBeTrue()
        ->and($receivedTestCase)->toBe($testCase)
        ->and($testCase->build()->variables)->toBe($variables);
});

test('alwaysExpect method callback can be used to add assertions', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];

    $testCase = $evaluation->alwaysExpect($variables, function (TestCase $tc) {
        $tc->toContain('test')
            ->toBeJudged('should be professional');
    });

    expect($testCase->build()->assertions)->toHaveCount(2);
});

test('alwaysExpect method callback is called on subsequent calls with existing test case', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];
    $callbackCount = 0;

    $testCase1 = $evaluation->alwaysExpect($variables, function (TestCase $tc) use (&$callbackCount) {
        $callbackCount++;
        $tc->toContain('first');
    });

    $testCase2 = $evaluation->alwaysExpect(['key2' => 'value2'], function (TestCase $tc) use (&$callbackCount) {
        $callbackCount++;
        $tc->toContain('second');
    });

    expect($testCase1)->toBe($testCase2)
        ->and($callbackCount)->toBe(2)
        ->and($testCase1->build()->assertions)->toHaveCount(2);
});

test('alwaysExpect method works without callback', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];

    $testCase = $evaluation->alwaysExpect($variables);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe($variables);
});

test('alwaysExpect method callback can be null', function () {
    $evaluation = new Evaluation(['prompt1']);
    $variables = ['key1' => 'value1'];

    $testCase = $evaluation->alwaysExpect($variables, null);

    expect($testCase)->toBeInstanceOf(TestCase::class)
        ->and($testCase->build()->variables)->toBe($variables);
});
