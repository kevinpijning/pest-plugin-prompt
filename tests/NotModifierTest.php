<?php

declare(strict_types=1);

use KevinPijning\Prompt\Evaluation;
use KevinPijning\Prompt\Promptfoo\ConfigBuilder;

test('not modifier negates the next assertion type and then resets', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase
        ->not->toContain('forbidden')
        ->toContain('allowed');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(2)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[1]->type)->toBe('icontains');
});

test('not modifier can be toggled twice to cancel negation', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->not->toContain('value');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('icontains');
});

test('not modifier works with strict mode toContain', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContain('value', strict: true);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-contains');
});

test('not modifier works with toContainAll', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContainAll(['value1', 'value2']);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains-all');
});

test('not modifier works with toContainAny', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContainAny(['value1', 'value2']);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains-any');
});

test('not modifier works with toContainJson', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContainJson();

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-contains-json');
});

test('not modifier works with toContainHtml', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContainHtml();

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-contains-html');
});

test('not modifier works with toContainSql', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContainSql();

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-contains-sql');
});

test('not modifier works with toContainXml', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContainXml();

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-contains-xml');
});

test('not modifier works with toBeJudged', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toBeJudged('rubric text');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-llm-rubric');
});

test('not modifier preserves value', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContain('value');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[0]->value)->toBe('value');
});

test('not modifier can be used multiple times independently', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase
        ->not->toContain('forbidden1')
        ->toContain('allowed1')
        ->not->toContain('forbidden2')
        ->toContain('allowed2');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(4)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[1]->type)->toBe('icontains')
        ->and($testCase->build()->assertions[2]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[3]->type)->toBe('icontains');
});

test('not modifier works with three consecutive not calls', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->not->not->toContain('value');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains');
});

test('not modifier works correctly in ConfigBuilder output', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();
    $testCase->not->toContain('forbidden');

    // Act
    $builder = ConfigBuilder::fromEvaluation($evaluation);
    $result = $builder->toArray();

    // Assert
    expect($result['tests'][0]['assert'][0]['type'])->toBe('not-icontains')
        ->and($result['tests'][0]['assert'][0]['value'])->toBe('forbidden');
});

test('not modifier with toContainAll preserves array value', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();
    $values = ['value1', 'value2'];

    // Act
    $testCase->not->toContainAll($values);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains-all')
        ->and($testCase->build()->assertions[0]->value)->toBe($values);
});

test('not modifier with strict toContainAll uses correct type', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContainAll(['value1', 'value2'], strict: true);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-contains-all');
});

test('not modifier with strict toContainAny uses correct type', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not->toContainAny(['value1', 'value2'], strict: true);

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-contains-any');
});

// Tests for not() method call syntax

test('not() method negates the next assertion type', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase
        ->not()->toContain('forbidden')
        ->toContain('allowed');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(2)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[1]->type)->toBe('icontains');
});

test('not() method can be toggled twice to cancel negation', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not()->not()->toContain('value');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('icontains');
});

test('not() method works with all assertion types', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase
        ->not()->toContain('value1')
        ->not()->toContainAll(['a', 'b'])
        ->not()->toContainAny(['c', 'd'])
        ->not()->toContainJson()
        ->not()->toBeJudged('rubric');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(5)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[1]->type)->toBe('not-icontains-all')
        ->and($testCase->build()->assertions[2]->type)->toBe('not-icontains-any')
        ->and($testCase->build()->assertions[3]->type)->toBe('not-contains-json')
        ->and($testCase->build()->assertions[4]->type)->toBe('not-llm-rubric');
});

test('not() method and not property can be mixed', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase
        ->not->toContain('property-negated')
        ->not()->toContain('method-negated')
        ->toContain('regular');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(3)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[1]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[2]->type)->toBe('icontains');
});

test('not() method preserves value', function () {
    // Arrange
    $evaluation = new Evaluation(['prompt']);
    $testCase = $evaluation->expect();

    // Act
    $testCase->not()->toContain('value');

    // Assert
    expect($testCase->build()->assertions)->toHaveCount(1)
        ->and($testCase->build()->assertions[0]->type)->toBe('not-icontains')
        ->and($testCase->build()->assertions[0]->value)->toBe('value');
});
