<?php

$api_key = base64_encode(env('DYNAMICS_USERNAME') . ':' . env('DYNAMICS_PASSWORD'));

return [

    'connection' => [
        'base_uri' => env('DYNAMICSBASEURL'),
        'timeout'  => env('DYNAMICS_TIMEOUT'),
        'headers'  => [
            'Authorization' => 'Basic ' . $api_key
        ]
    ],

    'cache' => [
        'seconds' => env('CACHE_DURATION_SECONDS', 120)
    ]



];
