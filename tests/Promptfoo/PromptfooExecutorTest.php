<?php

declare(strict_types=1);

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Promptfoo\EvaluationContext;
use KevinPijning\Prompt\Promptfoo\Promptfoo;
use KevinPijning\Prompt\Promptfoo\PromptfooConfiguration;
use KevinPijning\Prompt\Promptfoo\PromptfooExecutor;

beforeEach(function () {
    Promptfoo::reset();
    // Clean up any test files
    $tempDir = sys_get_temp_dir();
    $files = glob($tempDir.'/promptfoo_*');
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
});

test('evaluate generates config file', function () {
    $evaluation = new Evaluation(['test prompt']);
    $evaluation->usingProvider('test-provider');
    $evaluation->expect(['var' => 'value'])->toContain('test');

    $config = new PromptfooConfiguration;
    $executor = new PromptfooExecutor($config);

    // Create a mock that writes the expected output
    $outputPath = sys_get_temp_dir().'/promptfoo_output_'.uniqid().'.json';
    $configPath = sys_get_temp_dir().'/promptfoo_config_'.uniqid().'.yaml';

    // Create a test command that writes the output file
    $testCommand = sprintf(
        'php -r "file_put_contents(\'%s\', json_encode([\'results\' => [\'results\' => []]])); echo \'Evaluation complete\';"',
        $outputPath
    );

    $testConfig = (new PromptfooConfiguration)->withCommand($testCommand);
    $executor = new PromptfooExecutor($testConfig);

    // We need to mock the EvaluationContext to use our test paths
    $reflection = new ReflectionClass(PromptfooExecutor::class);

    // Actually, let's test the buildCommand method via reflection
    $context = EvaluationContext::create($evaluation, $config);

    // Test buildCommand
    $buildCommandMethod = $reflection->getMethod('buildCommand');
    $command = $buildCommandMethod->invoke($executor, $context);

    expect($command)->toBeArray()
        ->and($command)->toContain('eval')
        ->and($command)->toContain('--config')
        ->and($command)->toContain('--output');
});

test('buildCommand includes user output path when provided', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = new PromptfooConfiguration;

    $context = new EvaluationContext(
        $evaluation,
        '/tmp/config.yaml',
        '/tmp/output.json',
        '/custom/output/path.html'
    );

    $executor = new PromptfooExecutor($config);
    $reflection = new ReflectionClass(PromptfooExecutor::class);
    $method = $reflection->getMethod('buildCommand');

    $command = $method->invoke($executor, $context);

    expect($command)->toContain('--output')
        ->and($command)->toContain('/custom/output/path.html');
});

test('cleanup removes config and output files', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = new PromptfooConfiguration;
    $context = EvaluationContext::create($evaluation, $config);

    // Create test files
    file_put_contents($context->configPath, 'test config');
    file_put_contents($context->outputPath, 'test output');

    expect(file_exists($context->configPath))->toBeTrue()
        ->and(file_exists($context->outputPath))->toBeTrue();

    $executor = new PromptfooExecutor($config);
    $reflection = new ReflectionClass(PromptfooExecutor::class);
    $method = $reflection->getMethod('cleanup');

    $method->invoke($executor, $context);

    expect(file_exists($context->configPath))->toBeFalse()
        ->and(file_exists($context->outputPath))->toBeFalse();
});

test('cleanup handles non-existent files gracefully', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = new PromptfooConfiguration;
    $context = EvaluationContext::create($evaluation, $config);

    // Ensure files don't exist
    if (file_exists($context->configPath)) {
        unlink($context->configPath);
    }
    if (file_exists($context->outputPath)) {
        unlink($context->outputPath);
    }

    $executor = new PromptfooExecutor($config);
    $reflection = new ReflectionClass(PromptfooExecutor::class);
    $method = $reflection->getMethod('cleanup');

    // Should not throw an error
    $method->invoke($executor, $context);

    expect(true)->toBeTrue(); // Just verify we got here without exception
});

test('executor uses configuration timeout', function () {
    $config = (new PromptfooConfiguration)->withTimeout(999);
    $executor = new PromptfooExecutor($config);

    $reflection = new ReflectionClass(PromptfooExecutor::class);
    $configProperty = $reflection->getProperty('config');
    $storedConfig = $configProperty->getValue($executor);

    expect($storedConfig->timeout())->toBe(999);
});

test('executor uses configuration command', function () {
    $config = (new PromptfooConfiguration)->withCommand('custom-command arg');
    $executor = new PromptfooExecutor($config);

    $evaluation = new Evaluation(['test prompt']);
    $context = EvaluationContext::create($evaluation, $config);

    $reflection = new ReflectionClass(PromptfooExecutor::class);
    $method = $reflection->getMethod('buildCommand');

    $command = $method->invoke($executor, $context);

    expect($command[0])->toBe('custom-command')
        ->and($command[1])->toBe('arg')
        ->and($command[2])->toBe('eval');
});
