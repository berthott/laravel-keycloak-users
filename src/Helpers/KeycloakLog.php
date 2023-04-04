<?php

namespace berthott\KeycloakUsers\Helpers;

use Illuminate\Support\Facades\Log;

class KeycloakLog
{
    public function log(string $message): void
    {
        //$this->line($message);
        Log::channel('keycloak')->info($message);
    }
}
