<?php

declare(strict_types=1);

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Promptfoo\EvaluationContext;
use KevinPijning\Prompt\Promptfoo\Promptfoo;

beforeEach(function () {
    Promptfoo::setOutputFolder(null);
});

test('create creates a PendingEvaluation instance', function () {
    $evaluation = new Evaluation(['test prompt']);

    $pending = EvaluationContext::create($evaluation);

    expect($pending)->toBeInstanceOf(EvaluationContext::class)
        ->and($pending->evaluation)->toBe($evaluation)
        ->and($pending->configPath)->toBeString()
        ->and($pending->outputPath)->toBeString()
        ->and($pending->userOutputPath)->toBeNull();
});

test('create generates unique config and output paths', function () {
    $evaluation = new Evaluation(['test prompt']);

    $pending1 = EvaluationContext::create($evaluation);
    $pending2 = EvaluationContext::create($evaluation);

    expect($pending1->configPath)->not->toBe($pending2->configPath)
        ->and($pending1->outputPath)->not->toBe($pending2->outputPath);
});

test('create sets userOutputPath when output folder is configured', function () {
    Promptfoo::setOutputFolder('/test/output');

    $evaluation = new Evaluation(['test prompt']);
    $evaluation->describe('Test Description');

    $pending = EvaluationContext::create($evaluation);

    expect($pending->userOutputPath)->not->toBeNull()
        ->and($pending->userOutputPath)->toContain('/test/output')
        ->and($pending->userOutputPath)->toEndWith('.html');
});

test('create uses test name when description is not set', function () {
    Promptfoo::setOutputFolder('/test/output');

    $evaluation = new Evaluation(['test prompt']);

    $pending = EvaluationContext::create($evaluation);

    expect($pending->userOutputPath)->not->toBeNull()
        ->and($pending->userOutputPath)->toContain('/test/output');
});
