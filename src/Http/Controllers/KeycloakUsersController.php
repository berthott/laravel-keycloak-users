<?php

namespace berthott\KeycloakUsers\Http\Controllers;

use berthott\KeycloakUsers\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Keycloak User API endpoint implementation.
 */
class KeycloakUsersController
{
    /**
     * Display the current user.
     * 
     * @api
     */
    public function current(): User
    {
        return Auth::user();
    }
}
