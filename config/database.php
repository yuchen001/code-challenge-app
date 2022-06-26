<?php

use Illuminate\Support\Str;

return [
    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ]
    ]
];
