<?php

namespace berthott\KeycloakUsers;

use berthott\KeycloakUsers\Facades\KeycloakUsers;
use berthott\KeycloakUsers\Models\User;
use berthott\KeycloakUsers\Observers\UserObserver;
use berthott\KeycloakUsers\Services\KeycloakUsersService;
use Illuminate\Support\ServiceProvider;

class KeycloakUsersServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
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
     *
     * @return void
     */
    public function boot()
    {

        // publish config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('keycloak-users.php'),
            __DIR__.'/../config/keycloakAdmin.php' => config_path('keycloakAdmin.php'),
        ], 'config');

        // load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/Migrations');
        
        // load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'keycloak-users');

        // observe user
        User::observe(UserObserver::class);

        // init singleton
        KeycloakUsers::init();
    }
    
    protected function routeConfiguration()
    {
        return [
            'middleware' => config('keycloak-users.middleware'),
        ];
    }
}
