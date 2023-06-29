<?php

namespace berthott\KeycloakUsers\Helpers;

use Illuminate\Support\Facades\Log;

/**
 * Logging helper class.
 */
class KeycloakLog
{
    /**
     * Log a message to the `keycloak` log.
     */
    public function log(string $message): void
    {
        Log::channel('keycloak')->info($message);
    }
}
