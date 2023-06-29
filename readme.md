# Laravel-Keycloak-Users 

Laravel user administration in Keycloak.

Keycloak user administration utilizing [haizad/laravel-keycloak-admin](https://github.com/haizad/laravel-keycloak-admin) for Keycloak API communication.
Working hand in hand with [robsontenorio/laravel-keycloak-guard](https://github.com/robsontenorio/laravel-keycloak-guard) and / or [Vizir/laravel-keycloak-web-guard](https://github.com/Vizir/laravel-keycloak-web-guard) for Keycloak authorization.

Creates a user representation on Laravel side and hooks into the model events to create the users in keycloak. Additionally syncs the current Keycloak state into Laravel.

## Keycloak as a guard for your Laravel application

You will most likely want to set up Keycloak as a `web` or `api` guard for you application.
You might set up [robsontenorio/laravel-keycloak-guard](https://github.com/robsontenorio/laravel-keycloak-guard) as an `api` guard and [Vizir/laravel-keycloak-web-guard](https://github.com/Vizir/laravel-keycloak-web-guard) as an `web`guard.
An example set up might be:

Installation:
```sh
composer require robsontenorio/laravel-keycloak-guard
composer require vizir/laravel-keycloak-web-guard
```

Your `.env` file:
```sh
# web + api
KEYCLOAK_BASE_URL=
# web
KEYCLOAK_REALM=
KEYCLOAK_REALM_PUBLIC_KEY=
KEYCLOAK_CLIENT_ID= # your web client id
KEYCLOAK_CLIENT_SECRET=
# api
KEYCLOAK_ALLOWED_RESOURCES=
```

Your `auth.php` config:
```php
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'keycloak-web',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'keycloak',
            'provider' => 'users',
            'hash' => false,
        ],
    ],
```

The `KEYCLOAK_CLIENT_ID` will be a Keycloak OpenID Connect client allowing `Standard flow` and `Direct access grants` and `Client authentification + Authorization` turned **OFF**. This means there will be no `KEYCLOAK_CLIENT_SECRET` set. This is our `web` client.

And additional Keycloak OpenID Connect client allowing `Standard flow` and `Direct access grants` and `Client authentification + Authorization` turned **ON** needs to be set up as our `api` client.
The `KEYCLOAK_ALLOWED_RESOURCES` is a string that will be verified by the `api` guard to be present inside the JWT token, that the Frontend received from the `web` client.

## Installation

### In Laravel
```sh
$ composer require berthott/laravel-keycloak-users
$ php artisan migrate
```

Additionaly to the guard environment variables set:

```sh
KEYCLOAK_ADMIN_CLIENT_ID= # your api client id
KEYCLOAK_ADMIN_CLIENT_SECRET= # your api client secret
```

### In Keycloak
Setup a client, with `Access Type: confidential` and `Service Accounts Enabled: true`.
Set `Realm Settings > Login > Edit Username: true`.
Add `manage-users, query-users, view-users` to `Roles > Default Roles > realm-management`.
Add the value chosen for the `api` guards `KEYCLOAK_ALLOWED_RESOURCES` to `Clients > (your Web Client) > Mappers` as Hardcoded Role.

## Usage

The package is loaded into Laravel automatically. A default user model is generated with ready to use [API Resource Routes](https://laravel.com/docs/8.x/controllers#api-resource-routes).

The package will add the following routes:
  * Index, *get*     `users/` => get all users
  * Show, *get*     `users/{user}` => get a single user
  * Create, *post*    `users/` => create a new user
  * Update, *put*    `users/{user}` => update an user
  * Destroy, *delete*  `users/{user}` => delete an user
  * Destroy many, *delete*  `users/destroy_many` => delete many users by their given ids
  * Schema, *get* `users/schema` => get the user table schema

### Changing the User Model

* Create your custom User model extending `berthott\KeycloakUsers\Models\User`
* Publish migration with `php artisan vendor:publish --provider="berthott\KeycloakUsers\KeycloakUsersServiceProvider" --tag="migrations"` and change it accordingly

### Changing the Welcome Email for new users

When creating a new user in Laravel a random password is generated and sent to the user. At the same time a new Keycloak user is generated with this very password. The password is temporary and is required to be updated on the first login.
To change the Welcome Email please use `php artisan vendor:publish --provider="berthott\KeycloakUsers\KeycloakUsersServiceProvider" --tag="views"`.

## Options

To change the default options use
```php
$ php artisan vendor:publish --provider="berthott\KeycloakUsers\KeycloakUsersServiceProvider" --tag="config"
```
* `mail.from.address`: From Address defaults to `'example@laravel-keycloak-users.com'`
* `mail.from.name`: From Name defaults to `env('APP_NAME')`
* `mail.subject`: Subject defaults to `'Welcome to '.env('APP_NAME')`
* `mail.link`: Link defaults to `env('APP_URL')`

## Dependencies

The `User` model is created the [laravel-crudable](https://docs.syspons-dev.com/laravel-crudable) package.


## Compatibility

Tested with Laravel 10.x and Keycloak 20.

## License

See [License File](license.md). Copyright © 2023 Jan Bladt.