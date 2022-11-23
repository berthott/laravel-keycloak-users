<?php

namespace berthott\KeycloakUsers\Exceptions;

use Exception;

class KeycloakNoUsersException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct()
    {
        parent::__construct('No users in keycloak realm');
    }
}
