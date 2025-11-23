<?php

use function Pest\Prompt\prompt;

test('oke', function () {
    $evaluation = prompt(
        'translate {{message}} to {{language}}',
        'Convert {{message}} to {{language}}',
    )->usingProvider('openai:gpt-4o-mini', 'openai:gpt-5-mini')
        ->describe('testing');

    $evaluation->expect([
        'message' => 'Hello World!',
        'language' => 'es',
    ])
        ->toContain('Hola')
        ->toContain('muda')
        ->and([
            'message' => 'Hello World!',
            'language' => 'nl',
        ])
        ->toContain('Hallo')
        ->toContain('wereld');
});

// describe('languages', function () {
//    prompt('nana {{oke}}')
//        ->usingProvider('openai:gpt-4o-mini');
//
//    test('one', function () {
//        prompt()->expect([
//            'message' => 'Hello World!',
//        ])->toContain('a;a');
//    });
//
//    test('two', function () {
//        prompt()->expect([
//            'message' => 'mooi weer!',
//        ])->toContain('ffs');
//    });
// });
