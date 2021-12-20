<?php

namespace berthott\KeycloakUsers;

use berthott\KeycloakUsers\Facades\KeycloakUsers;
use berthott\KeycloakUsers\Http\Controllers\KeycloakUsersController;
use berthott\KeycloakUsers\Services\KeycloakUsersService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class KeycloakUsersServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // bind singleton
        $this->app->singleton('KeycloakUsers', function () {
            return new KeycloakUsersService();
        });

        // add config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'keycloak-users');
        $this->mergeConfigFrom(__DIR__.'/../config/keycloakAdmin.php', 'keycloakAdmin');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // publish config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('keycloak-users.php'),
            __DIR__.'/../config/keycloakAdmin.php' => config_path('keycloakAdmin.php'),
        ], 'config');

        // load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations/2014_10_12_000000_create_users_table.php' => database_path('migrations/2014_10_12_000000_create_users_table.php'),
        ], 'migrations');

        // load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'keycloak-users');

        // publish view
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/keycloak-users'),
        ], 'views');

        // add routes
        Route::group($this->routeConfiguration(), function () {
            Route::get('currentUser', [KeycloakUsersController::class, 'current'])->name('users.current');
        });

        // init singleton
        KeycloakUsers::init();
    }

    protected function routeConfiguration(): array
    {
        return [
            'middleware' => config('crudable.middleware'),
            'prefix' => config('crudable.prefix'),
        ];
    }
}
