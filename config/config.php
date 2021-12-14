<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | Configurations for the route.
    |
    */

    'middleware' => ['api'],

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
        'subject' => 'Welcome to '.env('APP_NAME'),
        'link' => env('APP_URL'),
    ],
];
