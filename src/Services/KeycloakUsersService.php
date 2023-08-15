<?php

namespace berthott\KeycloakUsers\Services;

use Facades\berthott\KeycloakUsers\Helpers\SyncHelper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

class KeycloakUsersService
{
    /**
     * Initialize the service.
     * 
     * Run the keycloak sync once.
     */
    public function init(): void
    {
        if (config('keycloak-users.auto_sync') && (!App::runningInConsole() || App::runningUnitTests()) && Schema::hasTable('users')) {
            SyncHelper::syncUsers();
        }
    }
}
