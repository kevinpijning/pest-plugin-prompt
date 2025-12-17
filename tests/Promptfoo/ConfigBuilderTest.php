<?php

declare(strict_types=1);

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Enums\FinishReason;
use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Promptfoo\ConfigBuilder;

test('fromEvaluation creates a ConfigBuilder instance', function () {
    $evaluation = new Evaluation(['prompt1']);
    $builder = ConfigBuilder::fromEvaluation($evaluation);

    expect($builder)->toBeInstanceOf(ConfigBuilder::class);
});

test('toArray returns array with all fields when populated', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $evaluation->describe('Test description');
    $evaluation->usingProvider('openai:gpt-4', 'anthropic:claude-3');

    $testCase = $evaluation->expect(['key1' => 'value1']);
    $testCase->assert(new Assertion('contains', 'expected value', 0.8, config: ['option1' => 'value1']));

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('description')
        ->and($result)->toHaveKey('prompts')
        ->and($result)->toHaveKey('providers')
        ->and($result)->toHaveKey('tests')
        ->and($result['description'])->toBe('Test description')
        ->and($result['prompts'])->toBe(['prompt1', 'prompt2'])
        ->and($result['providers'])->toBe([['id' => 'openai:gpt-4'], ['id' => 'anthropic:claude-3']])
        ->and($result['tests'])->toBeArray()
        ->and($result['tests'])->toHaveCount(1)
        ->and($result['tests'][0])->toHaveKey('vars')
        ->and($result['tests'][0])->toHaveKey('assert')
        ->and($result['tests'][0]['vars'])->toBe(['key1' => 'value1'])
        ->and($result['tests'][0]['assert'])->toBeArray()
        ->and($result['tests'][0]['assert'])->toHaveCount(1)
        ->and($result['tests'][0]['assert'][0])->toHaveKey('type')
        ->and($result['tests'][0]['assert'][0])->toHaveKey('value')
        ->and($result['tests'][0]['assert'][0])->toHaveKey('threshold')
        ->and($result['tests'][0]['assert'][0])->toHaveKey('config')
        ->and($result['tests'][0]['assert'][0]['type'])->toBe('contains')
        ->and($result['tests'][0]['assert'][0]['value'])->toBe('expected value')
        ->and($result['tests'][0]['assert'][0]['threshold'])->toBe(0.8)
        ->and($result['tests'][0]['assert'][0]['config'])->toBe(['option1' => 'value1']);
});

test('toArray filters out null description', function () {
    $evaluation = new Evaluation(['prompt1']);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result)->not->toHaveKey('description');
});

test('toArray filters out empty providers array', function () {
    $evaluation = new Evaluation(['prompt1']);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result)->not->toHaveKey('providers');
});

test('toArray filters out null threshold in assertions', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->assert(new Assertion('contains', 'test'));

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0]['assert'][0])->not->toHaveKey('threshold');
});

test('toArray filters out null config in assertions', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->assert(new Assertion('contains', 'test', null, null, null, null, null, null, null));

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0]['assert'][0])->not->toHaveKey('config');
});

test('toArray handles multiple test cases', function () {
    $evaluation = new Evaluation(['prompt1']);
    $evaluation->expect(['key1' => 'value1'])->toContain('test1');
    $evaluation->expect(['key2' => 'value2'])->toContain('test2');
    $evaluation->expect(['key3' => 'value3'])->toContain('test3');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'])->toHaveCount(3)
        ->and($result['tests'][0]['vars'])->toBe(['key1' => 'value1'])
        ->and($result['tests'][1]['vars'])->toBe(['key2' => 'value2'])
        ->and($result['tests'][2]['vars'])->toBe(['key3' => 'value3']);
});

test('toArray handles multiple assertions per test case', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->toContain('test1');
    $testCase->toContain('test2');
    $testCase->toContain('test3');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0]['assert'])->toHaveCount(3)
        ->and($result['tests'][0]['assert'][0]['value'])->toBe('test1')
        ->and($result['tests'][0]['assert'][1]['value'])->toBe('test2')
        ->and($result['tests'][0]['assert'][2]['value'])->toBe('test3');
});

test('toArray filters out empty test cases array', function () {
    $evaluation = new Evaluation(['prompt1']);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result)->not->toHaveKey('tests');
});

test('toArray filters out empty variables array', function () {
    $evaluation = new Evaluation(['prompt1']);
    $evaluation->expect([])->toContain('test');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0])->not->toHaveKey('vars');
});

test('toArray handles test case with no assertions', function () {
    $evaluation = new Evaluation(['prompt1']);
    $evaluation->expect(['key' => 'value']);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0])->not->toHaveKey('assert');
});

test('toYaml returns valid YAML string', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $evaluation->describe('Test description');
    $evaluation->usingProvider('openai:gpt-4');

    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->toContain('test', true);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $yaml = $builder->toYaml();

    expect($yaml)->toBeString()
        ->and($yaml)->toContain("description: 'Test description'")
        ->and($yaml)->toContain('prompts:')
        ->and($yaml)->toContain('- prompt1')
        ->and($yaml)->toContain('- prompt2')
        ->and($yaml)->toContain('providers:')
        ->and($yaml)->toContain("'openai:gpt-4'")
        ->and($yaml)->toContain('tests:');
});

test('toYaml filters out empty values', function () {
    $evaluation = new Evaluation(['prompt1']);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $yaml = $builder->toYaml();

    expect($yaml)->toBeString()
        ->and($yaml)->not->toContain('description:')
        ->and($yaml)->not->toContain('providers:');
});

test('toYaml handles complex nested structure', function () {
    $evaluation = new Evaluation(['prompt1', 'prompt2']);
    $evaluation->describe('Complex test');
    $evaluation->usingProvider('openai:gpt-4', 'anthropic:claude-3');

    $testCase1 = $evaluation->expect(['var1' => 'value1', 'var2' => 'value2']);
    $testCase1->toContain('expected1', true);
    $testCase1->toContain('expected2', false);

    $testCase2 = $evaluation->expect(['var3' => 'value3']);
    $testCase2->toContain('expected3');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $yaml = $builder->toYaml();

    expect($yaml)->toBeString()
        ->and($yaml)->toContain("description: 'Complex test'")
        ->and($yaml)->toContain('prompts:')
        ->and($yaml)->toContain('providers:')
        ->and($yaml)->toContain('tests:');
});

test('toArray handles assertion with only type and value', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->assert(new Assertion('contains', 'test value'));

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0]['assert'][0])->toHaveKey('type')
        ->and($result['tests'][0]['assert'][0])->toHaveKey('value')
        ->and($result['tests'][0]['assert'][0]['type'])->toBe('contains')
        ->and($result['tests'][0]['assert'][0]['value'])->toBe('test value')
        ->and($result['tests'][0]['assert'][0])->not->toHaveKey('threshold')
        ->and($result['tests'][0]['assert'][0])->not->toHaveKey('config');
});

test('toArray handles assertion with threshold but no config', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->assert(new Assertion('contains', 'test', 0.75));

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0]['assert'][0])->toHaveKey('threshold')
        ->and($result['tests'][0]['assert'][0]['threshold'])->toBe(0.75)
        ->and($result['tests'][0]['assert'][0])->not->toHaveKey('config');
});

test('toArray handles assertion with config but no threshold', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->assert(new Assertion('contains', 'test', null, config: ['key' => 'value']));

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0]['assert'][0])->not->toHaveKey('threshold')
        ->and($result['tests'][0]['assert'][0])->toHaveKey('config')
        ->and($result['tests'][0]['assert'][0]['config'])->toBe(['key' => 'value']);
});

test('toArray handles FinishReason enum correctly', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->toHaveFinishReason(FinishReason::Stop);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0]['assert'][0])->toHaveKey('type')
        ->and($result['tests'][0]['assert'][0])->toHaveKey('value')
        ->and($result['tests'][0]['assert'][0]['type'])->toBe('finish-reason')
        ->and($result['tests'][0]['assert'][0]['value'])->toBe('stop')
        ->and($result['tests'][0]['assert'][0]['value'])->toBeString();
});

test('toArray handles all FinishReason enum values correctly', function () {
    $evaluation = new Evaluation(['prompt1']);
    $testCase = $evaluation->expect(['key' => 'value']);
    $testCase->toHaveFinishReason(FinishReason::Stop);
    $testCase->toHaveFinishReason(FinishReason::Length);
    $testCase->toHaveFinishReason(FinishReason::ContentFilter);
    $testCase->toHaveFinishReason(FinishReason::ToolCalls);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['tests'][0]['assert'])->toHaveCount(4)
        ->and($result['tests'][0]['assert'][0]['value'])->toBe('stop')
        ->and($result['tests'][0]['assert'][1]['value'])->toBe('length')
        ->and($result['tests'][0]['assert'][2]['value'])->toBe('content_filter')
        ->and($result['tests'][0]['assert'][3]['value'])->toBe('tool_calls');
});

test('toArray includes defaultTest when alwaysExpect is used', function () {
    $evaluation = new Evaluation(['prompt1']);
    $evaluation->alwaysExpect(['default' => 'value'])
        ->toBeJudged('test assertion');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result)->toHaveKey('defaultTest')
        ->and($result['defaultTest'])->toBeArray()
        ->and($result['defaultTest'])->toHaveKey('vars')
        ->and($result['defaultTest'])->toHaveKey('assert')
        ->and($result['defaultTest']['vars'])->toBe(['default' => 'value'])
        ->and($result['defaultTest']['assert'])->toBeArray()
        ->and($result['defaultTest']['assert'])->toHaveCount(1);
});

test('toArray filters out defaultTest when not set', function () {
    $evaluation = new Evaluation(['prompt1']);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result)->not->toHaveKey('defaultTest');
});

test('toArray includes defaultTest vars correctly', function () {
    $evaluation = new Evaluation(['prompt1']);
    $evaluation->alwaysExpect(['key1' => 'value1', 'key2' => 'value2']);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['defaultTest']['vars'])->toBe(['key1' => 'value1', 'key2' => 'value2']);
});

test('toArray includes defaultTest assertions correctly', function () {
    $evaluation = new Evaluation(['prompt1']);
    $defaultTestCase = $evaluation->alwaysExpect(['default' => 'value']);
    $defaultTestCase->toBeJudged('first assertion');
    $defaultTestCase->toContain('second assertion');
    $defaultTestCase->toBeJudged('third assertion', 0.8);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['defaultTest']['assert'])->toHaveCount(3)
        ->and($result['defaultTest']['assert'][0]['type'])->toBe('llm-rubric')
        ->and($result['defaultTest']['assert'][0]['value'])->toBe('first assertion')
        ->and($result['defaultTest']['assert'][1]['type'])->toBe('icontains')
        ->and($result['defaultTest']['assert'][1]['value'])->toBe('second assertion')
        ->and($result['defaultTest']['assert'][2]['type'])->toBe('llm-rubric')
        ->and($result['defaultTest']['assert'][2]['value'])->toBe('third assertion')
        ->and($result['defaultTest']['assert'][2]['threshold'])->toBe(0.8);
});

test('toArray filters out empty vars in defaultTest', function () {
    $evaluation = new Evaluation(['prompt1']);
    $evaluation->alwaysExpect([])->toBeJudged('test');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['defaultTest'])->not->toHaveKey('vars');
});

test('toArray filters out empty assert in defaultTest', function () {
    $evaluation = new Evaluation(['prompt1']);
    $evaluation->alwaysExpect(['key' => 'value']);

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['defaultTest'])->not->toHaveKey('assert');
});

test('toArray handles defaultTest with both vars and assert', function () {
    $evaluation = new Evaluation(['prompt1']);
    $evaluation->alwaysExpect(['default' => 'value', 'another' => 'test'])
        ->toBeJudged('assertion 1')
        ->toContain('assertion 2');

    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    expect($result['defaultTest'])->toHaveKey('vars')
        ->and($result['defaultTest'])->toHaveKey('assert')
        ->and($result['defaultTest']['vars'])->toBe(['default' => 'value', 'another' => 'test'])
        ->and($result['defaultTest']['assert'])->toHaveCount(2);
});
