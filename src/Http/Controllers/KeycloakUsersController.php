<?php

namespace berthott\KeycloakUsers\Http\Controllers;

use berthott\KeycloakUsers\Models\User;
use Illuminate\Support\Facades\Auth;

class KeycloakUsersController
{
    /**
     * Display the current user.
     */
    public function current(): User
    {
        return Auth::user();
    }
}
