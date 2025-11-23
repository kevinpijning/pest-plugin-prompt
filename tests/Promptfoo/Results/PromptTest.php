<?php

declare(strict_types=1);

use Pest\Prompt\Promptfoo\Results\Prompt;

test('Prompt can be instantiated with raw and label', function () {
    $prompt = new Prompt(
        raw: 'translate Hello World! to es',
        label: 'translate {{message}} to {{language}}',
    );

    expect($prompt->raw)->toBe('translate Hello World! to es')
        ->and($prompt->label)->toBe('translate {{message}} to {{language}}');
});

test('Prompt can have empty label', function () {
    $prompt = new Prompt(
        raw: 'test prompt',
        label: '',
    );

    expect($prompt->raw)->toBe('test prompt')
        ->and($prompt->label)->toBe('');
});
