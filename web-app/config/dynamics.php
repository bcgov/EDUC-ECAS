<?php

return [

    'connection' => [
        'base_uri' => env('DYNAMICSBASEURL'),
        'timeout'  => env('DYNAMICS_TIMEOUT'),
        'headers'  => [
            'Authorization' => env('API_KEY')
        ]
    ],

    'cache' => [
        'seconds' => env('CACHE_DURATION_SECONDS', 28800)
    ]



];
