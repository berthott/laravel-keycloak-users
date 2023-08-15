<?php

namespace berthott\KeycloakUsers\Console;

use Facades\berthott\KeycloakUsers\Helpers\SyncHelper;
use Illuminate\Console\Command;

/**
 * An artisan command to sync keycloak users.
 * 
 * @api
 */
class Sync extends Command
{
    /**
     * The Signature.
     * 
     * @api
     */
    protected $signature = 'keycloak:sync';
    protected $description = 'Sync keycloak users';

    public function handle()
    {
        SyncHelper::syncUsers();
    }
}
