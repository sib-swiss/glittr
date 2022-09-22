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
    'apis' => [
        'github' => [
            'driver' => 'github',
            'connection' => 'main',
            'hosts' => [
                'github.com',
                'www.github.com',
            ],
        ],
        'gitlab' => [
            'driver' => 'gitlab',
            'connection' => 'main',
            'hosts' => [
                'gitlab.com',
                'www.gitlab.com',
            ],
        ],
    ],
];
