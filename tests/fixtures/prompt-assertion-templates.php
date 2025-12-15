<?php

declare(strict_types=1);

return [
    'mentionsCoffee' => [
        'type' => 'icontains',
        'value' => 'coffee',
    ],
    'prefersLaravelOverNextJs' => [
        'type' => 'llm-rubric',
        'value' => 'The answer states a clear preference for Laravel over Next.js.',
        'threshold' => 0.9,
    ],
];
