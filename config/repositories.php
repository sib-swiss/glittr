<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repositories sources
    |--------------------------------------------------------------------------
    |
    | Define all sources for the repositories with the driver and connection settings to use.
    | The manager will determine which source to use depending on the domain of the repository url.
    |
    |
    */
    'sources' => [
        'github' => [
            'driver' => 'github',
            'connection' => 'main',
            'domains' => [
                'github.com',
                'www.github.com',
            ],
        ],
        'gitlab' => [
            'driver' => 'gitlab',
            'connection' => 'main',
            'domains' => [
                'gitblab.com',
                'www.gitlab.com',
            ],
        ],
    ],
];
