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
];
