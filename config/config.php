<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Configuration
    |--------------------------------------------------------------------------
    |
    | Configurations for the new user email.
    |
    */

    'mail' => [
        'from' => [
            'address' => 'example@laravel-keycloak-users.com',
            'name' => env('APP_NAME'),
        ],
        'replyTo' => [
            'address' => 'replyTo@laravel-keycloak-users.com',
            'name' => env('APP_NAME'),
        ],
        'subject' => 'Welcome to '.env('APP_NAME'),
        'link' => env('APP_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto sync
    |--------------------------------------------------------------------------
    |
    | The local laravel users will be synced with the keycloak users
    | on every request. This will slow down the request performance
    | significantly. Use keycloak:sync instead.
    |
    */

    'auto_sync' => env('KEYCLOAK_USERS_AUTO_SYNC', false),
];
