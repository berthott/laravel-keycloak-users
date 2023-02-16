<?php

namespace berthott\KeycloakUsers\Tests\Feature\KeycloakUsersTest;

use berthott\Crudable\CrudableServiceProvider;
use berthott\KeycloakUsers\KeycloakUsersServiceProvider;
use berthott\Scopeable\ScopeableServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use LaravelKeycloakAdmin\KeycloakAdminServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class KeycloakUsersTestCase extends BaseTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            KeycloakUsersServiceProvider::class,
            CrudableServiceProvider::class,
            ScopeableServiceProvider::class,
            KeycloakAdminServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        Config::set('crudable.namespace', __NAMESPACE__);
    }
}
