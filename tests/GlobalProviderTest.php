<?php

use KevinPijning\Prompt\Api\Provider;

provider('testProvider', function (Provider $provider) {
    return $provider->id('openai:gpt-4o-mini')
        ->label('lala');
});

provider('testProvider2')
    ->id('openai:gpt-4o-mini')
    ->label('heehee')
    ->config([
        'aopi' => 'key',
    ]);

test('it can use providers', function () {

    prompt('translate')
        ->usingProvider('testProvider', 'testProvider');

    prompt('translate')
        ->usingProvider('testProvider');

    prompt('translate')
        ->usingProvider(function (Provider $provider) {
            return $provider->id('openai:gpt-4o-mini')
                ->label('nice')
                ->topP(.4);
        });

    prompt('translate')
        ->usingProvider('openai:gpt-4o-mini');

    prompt('translate')
        ->usingProvider(
            Provider::create('openai:gpt-4o-mini')
                ->label('nice')
        );

});
