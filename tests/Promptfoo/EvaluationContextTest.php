<?php

declare(strict_types=1);

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Promptfoo\EvaluationContext;
use KevinPijning\Prompt\Promptfoo\Promptfoo;
use KevinPijning\Prompt\Promptfoo\PromptfooConfiguration;

beforeEach(function () {
    Promptfoo::reset();
});

test('create creates an EvaluationContext instance', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = new PromptfooConfiguration;

    $context = EvaluationContext::create($evaluation, $config);

    expect($context)->toBeInstanceOf(EvaluationContext::class)
        ->and($context->evaluation)->toBe($evaluation)
        ->and($context->configPath)->toBeString()
        ->and($context->outputPath)->toBeString()
        ->and($context->userOutputPath)->toBeNull();
});

test('create generates unique config and output paths', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = new PromptfooConfiguration;

    $context1 = EvaluationContext::create($evaluation, $config);
    $context2 = EvaluationContext::create($evaluation, $config);

    expect($context1->configPath)->not->toBe($context2->configPath)
        ->and($context1->outputPath)->not->toBe($context2->outputPath);
});

test('create sets userOutputPath when output folder is configured', function () {
    $evaluation = new Evaluation(['test prompt']);
    $evaluation->describe('Test Description');
    $config = (new PromptfooConfiguration)->withOutputFolder('/test/output');

    $context = EvaluationContext::create($evaluation, $config);

    expect($context->userOutputPath)->not->toBeNull()
        ->and($context->userOutputPath)->toContain('/test/output')
        ->and($context->userOutputPath)->toEndWith('.html');
});

test('create uses test name when description is not set', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = (new PromptfooConfiguration)->withOutputFolder('/test/output');

    $context = EvaluationContext::create($evaluation, $config);

    expect($context->userOutputPath)->not->toBeNull()
        ->and($context->userOutputPath)->toContain('/test/output');
});

test('config paths are in temp directory', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = new PromptfooConfiguration;

    $context = EvaluationContext::create($evaluation, $config);

    expect($context->configPath)->toContain(sys_get_temp_dir())
        ->and($context->outputPath)->toContain(sys_get_temp_dir());
});

test('config path has yaml extension', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = new PromptfooConfiguration;

    $context = EvaluationContext::create($evaluation, $config);

    expect($context->configPath)->toEndWith('.yaml');
});

test('output path has json extension', function () {
    $evaluation = new Evaluation(['test prompt']);
    $config = new PromptfooConfiguration;

    $context = EvaluationContext::create($evaluation, $config);

    expect($context->outputPath)->toEndWith('.json');
});
