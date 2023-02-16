# Laravel-Keycloak-Users - Laravel user administration in Keycloak

User Keycloak administration working hand in hand with [robsontenorio/laravel-keycloak-guard](https://github.com/robsontenorio/laravel-keycloak-guard) and / or [Vizir/laravel-keycloak-web-guard](https://github.com/Vizir/laravel-keycloak-web-guard). for the Keycloak Authorization.
Creates a user representation on Laravel side and hooks into the model events to create the users in keycloak. Additionally syncs the current Keycloak state into Laravel.

## Installation

### In Laravel
```
$ composer require berthott/laravel-keycloak-users
$ php artisan migrate
```

Additional to the guard environment varibale set 
```
KEYCLOAK_ADMIN_CLIENT_ID=
KEYCLOAK_ADMIN_CLIENT_SECRET=
```

### In Keycloak
Setup a client, with `Access Type: confidential` and `Service Accounts Enabled: true`.
Set `Realm Settings > Login > Edit Username: true`.
Add `manage-users, query-users, view-users` to `Roles > Default Roles > realm-management`.
Add the value chosen for keycloak-webs KEYCLOAK_ALLOWED_RESOURCES to `Clients > (your Web Client) > Mappers` as Hardcoded Role.

## Usage

The package is loaded into Laravel automatically. A default User model is generated with ready to use [API Resource Routes](https://laravel.com/docs/8.x/controllers#api-resource-routes).

### Changing the User Model

* Create your custom User model extending `berthott\KeycloakUsers\Models\User`
* Publish migration with `php artisan vendor:publish --provider="berthott\KeycloakUsers\KeycloakUsersServiceProvider" --tag="migrations"` and change it accordingly

### Changing the Welcome Email for new users

When creating a new User in Laravel a random password is generated and sent to the user. At the same time a new Keycloak User is generated with this very password. The password is temporary and is required to be updated on the first login.
To change the Welcome Email please use `php artisan vendor:publish --provider="berthott\KeycloakUsers\KeycloakUsersServiceProvider" --tag="views"`.

## Options

To change the default options use
```
$ php artisan vendor:publish --provider="berthott\KeycloakUsers\KeycloakUsersServiceProvider" --tag="config"
```
* `middleware`: an array of middlewares that will be added to the generated routes
* `mail.from.address`: From Address defaults to `'example@laravel-keycloak-users.com'`
* `mail.from.name`: From Name defaults to `env('APP_NAME')`
* `mail.subject`: Subject defaults to `'Welcome to '.env('APP_NAME')`
* `mail.link`: Link defaults to `env('APP_URL')`

## Compatibility

Tested with Laravel 10.x and Keycloak 20.

## License

See [License File](license.md). Copyright © 2023 Jan Bladt.