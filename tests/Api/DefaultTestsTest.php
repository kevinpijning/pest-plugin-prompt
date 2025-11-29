<?php

declare(strict_types=1);

use Pest\Prompt\Api\Evaluation;
use Pest\Prompt\Promptfoo\ConfigBuilder;

test('default tests are separated from test cases with variables', function () {
    $evaluation = new Evaluation([
        'translate {{message}} to {{language}}',
    ]);

    // Create a default test (no variables)
    $evaluation->expect()
        ->describe('Always answer formal')
        ->toBeJudged('q3g4');

    // Create a test case with variables
    $evaluation->expect(['language' => 'en', 'message' => 'Hello World!'])
        ->toContain('hello');

    expect($evaluation->defaultTests())->toHaveCount(1)
        ->and($evaluation->testCases())->toHaveCount(1)
        ->and($evaluation->defaultTests()[0]->variables())->toBe([])
        ->and($evaluation->defaultTests()[0]->description())->toBe('Always answer formal')
        ->and($evaluation->testCases()[0]->variables())->toBe(['language' => 'en', 'message' => 'Hello World!']);
});

test('default tests appear in defaultTest key in config', function () {
    $evaluation = new Evaluation([
        'translate {{message}} to {{language}}',
    ]);

    // Create a default test
    $evaluation->expect()
        ->describe('Always answer formal')
        ->toBeJudged('q3g4');

    // Create a test case with variables
    $evaluation->expect(['language' => 'en', 'message' => 'Hello World!'])
        ->toContain('hello');

    $config = ConfigBuilder::fromEvaluation($evaluation)->toArray();

    expect($config)->toHaveKey('defaultTest')
        ->and($config)->toHaveKey('tests')
        ->and($config['defaultTest'])->toBeArray()
        ->and($config['defaultTest'])->toHaveCount(1)
        ->and($config['tests'])->toBeArray()
        ->and($config['tests'])->toHaveCount(1)
        ->and($config['defaultTest'][0])->toHaveKey('description')
        ->and($config['defaultTest'][0])->toHaveKey('assert')
        ->and($config['defaultTest'][0]['description'])->toBe('Always answer formal')
        ->and($config['defaultTest'][0])->not->toHaveKey('vars')
        ->and($config['tests'][0])->toHaveKey('vars')
        ->and($config['tests'][0]['vars'])->toBe(['language' => 'en', 'message' => 'Hello World!']);
});

test('defaultTest key is filtered out when no default tests exist', function () {
    $evaluation = new Evaluation(['prompt1']);

    // Only create test cases with variables
    $evaluation->expect(['key' => 'value'])->toContain('test');

    $config = ConfigBuilder::fromEvaluation($evaluation)->toArray();

    expect($config)->not->toHaveKey('defaultTest')
        ->and($config)->toHaveKey('tests');
});

test('tests key is filtered out when no test cases with variables exist', function () {
    $evaluation = new Evaluation(['prompt1']);

    // Only create default tests
    $evaluation->expect()->toContain('test');

    $config = ConfigBuilder::fromEvaluation($evaluation)->toArray();

    expect($config)->toHaveKey('defaultTest')
        ->and($config)->not->toHaveKey('tests');
});

test('multiple default tests are all included in defaultTest', function () {
    $evaluation = new Evaluation(['prompt1']);

    $evaluation->expect()->describe('First default')->toContain('test1');
    $evaluation->expect()->describe('Second default')->toContain('test2');
    $evaluation->expect()->describe('Third default')->toContain('test3');

    $config = ConfigBuilder::fromEvaluation($evaluation)->toArray();

    expect($config['defaultTest'])->toHaveCount(3)
        ->and($config['defaultTest'][0]['description'])->toBe('First default')
        ->and($config['defaultTest'][1]['description'])->toBe('Second default')
        ->and($config['defaultTest'][2]['description'])->toBe('Third default');
});

test('mapDefaultTests returns array of default tests', function () {
    $evaluation = new Evaluation(['prompt1']);

    $evaluation->expect()->describe('Default test')->toContain('test');
    $evaluation->expect(['key' => 'value'])->toContain('test');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $defaultTests = $builder->mapDefaultTests();

    expect($defaultTests)->toBeArray()
        ->and($defaultTests)->toHaveCount(1)
        ->and($defaultTests[0])->toHaveKey('description')
        ->and($defaultTests[0]['description'])->toBe('Default test')
        ->and($defaultTests[0])->not->toHaveKey('vars');
});

test('mapTestCases returns array of test cases with variables', function () {
    $evaluation = new Evaluation(['prompt1']);

    $evaluation->expect()->toContain('test');
    $evaluation->expect(['key1' => 'value1'])->describe('Test case 1')->toContain('test1');
    $evaluation->expect(['key2' => 'value2'])->describe('Test case 2')->toContain('test2');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $testCases = $builder->mapTestCases();

    expect($testCases)->toBeArray()
        ->and($testCases)->toHaveCount(2)
        ->and($testCases[0])->toHaveKey('vars')
        ->and($testCases[0]['vars'])->toBe(['key1' => 'value1'])
        ->and($testCases[1]['vars'])->toBe(['key2' => 'value2']);
});
