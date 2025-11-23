<?php

declare(strict_types=1);

use Pest\Prompt\Api\Assertion;
use Pest\Prompt\Promptfoo\Results\ComponentResult;

test('ComponentResult can be instantiated with all properties', function () {
    $assertion = new Assertion('contains', 'test');
    $componentResult = new ComponentResult(
        pass: true,
        score: 1.0,
        reason: 'Assertion passed',
        assertion: $assertion,
    );

    expect($componentResult->pass)->toBeTrue()
        ->and($componentResult->score)->toBe(1.0)
        ->and($componentResult->reason)->toBe('Assertion passed')
        ->and($componentResult->assertion)->toBe($assertion);
});

test('ComponentResult can be instantiated with false pass', function () {
    $assertion = new Assertion('icontains', 'muda');
    $componentResult = new ComponentResult(
        pass: false,
        score: 0.0,
        reason: 'Expected output to contain "muda"',
        assertion: $assertion,
    );

    expect($componentResult->pass)->toBeFalse()
        ->and($componentResult->score)->toBe(0.0)
        ->and($componentResult->reason)->toBe('Expected output to contain "muda"')
        ->and($componentResult->assertion->type)->toBe('icontains')
        ->and($componentResult->assertion->value)->toBe('muda');
});
