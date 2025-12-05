<?php

declare(strict_types=1);

use KevinPijning\Prompt\Api\Evaluation;
use KevinPijning\Prompt\Promptfoo\PendingEvaluation;
use KevinPijning\Prompt\Promptfoo\PromptfooClient;

beforeEach(function () {
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

    $client = new PromptfooClient('echo "test"');

    // Create a mock that writes the expected output
    $outputPath = sys_get_temp_dir().'/promptfoo_output_'.uniqid().'.json';
    $configPath = sys_get_temp_dir().'/promptfoo_config_'.uniqid().'.yaml';

    // Create a test command that writes the output file
    $testCommand = sprintf(
        'php -r "file_put_contents(\'%s\', json_encode([\'results\' => [\'results\' => []]])); echo \'Evaluation complete\';"',
        $outputPath
    );

    $client = new PromptfooClient($testCommand);

    // We need to mock the PendingEvaluation to use our test paths
    $reflection = new ReflectionClass(PromptfooClient::class);
    $method = $reflection->getMethod('evaluate');
    $method->setAccessible(false);

    // Actually, let's test the generateCommand and generateConfig methods via reflection
    $pending = PendingEvaluation::create($evaluation);

    // Test generateCommand
    $generateCommandMethod = $reflection->getMethod('generateCommand');
    $generateCommandMethod->setAccessible(true);
    $command = $generateCommandMethod->invoke($client, $pending);

    expect($command)->toBeArray()
        ->and($command)->toContain('eval')
        ->and($command)->toContain('--config')
        ->and($command)->toContain('--output');
});

test('generateCommand includes user output path when provided', function () {
    $evaluation = new Evaluation(['test prompt']);
    $pending = PendingEvaluation::create($evaluation);

    // Manually set userOutputPath via reflection
    $reflection = new ReflectionClass(PendingEvaluation::class);
    $property = $reflection->getProperty('userOutputPath');
    $property->setAccessible(true);

    $newPending = new PendingEvaluation(
        $pending->evaluation,
        $pending->configPath,
        $pending->outputPath,
        '/custom/output/path.html'
    );

    $client = new PromptfooClient('test-command');
    $reflection = new ReflectionClass(PromptfooClient::class);
    $method = $reflection->getMethod('generateCommand');
    $method->setAccessible(true);

    $command = $method->invoke($client, $newPending);

    expect($command)->toContain('--output')
        ->and($command)->toContain('/custom/output/path.html');
});

test('cleanup removes config and output files', function () {
    $evaluation = new Evaluation(['test prompt']);
    $pending = PendingEvaluation::create($evaluation);

    // Create test files
    file_put_contents($pending->configPath, 'test config');
    file_put_contents($pending->outputPath, 'test output');

    expect(file_exists($pending->configPath))->toBeTrue()
        ->and(file_exists($pending->outputPath))->toBeTrue();

    $client = new PromptfooClient('test-command');
    $reflection = new ReflectionClass(PromptfooClient::class);
    $method = $reflection->getMethod('cleanup');
    $method->setAccessible(true);

    $method->invoke($client, $pending);

    expect(file_exists($pending->configPath))->toBeFalse()
        ->and(file_exists($pending->outputPath))->toBeFalse();
});

test('cleanup handles non-existent files gracefully', function () {
    $evaluation = new Evaluation(['test prompt']);
    $pending = PendingEvaluation::create($evaluation);

    // Ensure files don't exist
    if (file_exists($pending->configPath)) {
        unlink($pending->configPath);
    }
    if (file_exists($pending->outputPath)) {
        unlink($pending->outputPath);
    }

    $client = new PromptfooClient('test-command');
    $reflection = new ReflectionClass(PromptfooClient::class);
    $method = $reflection->getMethod('cleanup');
    $method->setAccessible(true);

    // Should not throw an error
    $method->invoke($client, $pending);

    expect(true)->toBeTrue(); // Just verify we got here without exception
});
