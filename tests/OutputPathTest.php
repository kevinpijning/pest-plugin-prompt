<?php

declare(strict_types=1);

use Pest\Prompt\OutputPath;

beforeEach(function () {
    OutputPath::clear();
});

test('has returns false by default', function () {
    expect(OutputPath::has())->toBeFalse();
});

test('get returns null by default', function () {
    expect(OutputPath::get())->toBeNull();
});

test('set sets the path', function () {
    $path = '/path/to/output.html';

    OutputPath::set($path);

    expect(OutputPath::has())->toBeTrue()
        ->and(OutputPath::get())->toBe($path);
});

test('clear resets the output path', function () {
    OutputPath::set('/path/to/output.html');

    OutputPath::clear();

    expect(OutputPath::has())->toBeFalse()
        ->and(OutputPath::get())->toBeNull();
});

test('set overwrites previous path', function () {
    OutputPath::set('/first/path.html');

    OutputPath::set('/second/path.html');

    expect(OutputPath::get())->toBe('/second/path.html');
});

test('with html fallback adds html extension when no extension is present', function () {
    expect(OutputPath::withHtmlFallback('path/to/results'))->toBe('path/to/results.html');
});

test('with html fallback does not change path when extension already exists', function () {
    expect(OutputPath::withHtmlFallback('path/to/results.html'))->toBe('path/to/results.html')
        ->and(OutputPath::withHtmlFallback('path/to/results.json'))->toBe('path/to/results.json')
        ->and(OutputPath::withHtmlFallback('path/to/results.csv'))->toBe('path/to/results.csv');
});

test('with html fallback generates filename with datetime for folder paths', function () {
    $result1 = OutputPath::withHtmlFallback('path/to/');
    $result2 = OutputPath::withHtmlFallback('pest-prompt-tests/');

    // Should start with folder path and end with datetime.html
    expect($result1)->toStartWith('path/to/')
        ->and($result1)->toEndWith('.html')
        ->and($result1)->toMatch('/\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}\.html$/')
        ->and($result2)->toStartWith('pest-prompt-tests/')
        ->and($result2)->toEndWith('.html')
        ->and($result2)->toMatch('/\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}\.html$/');
});
