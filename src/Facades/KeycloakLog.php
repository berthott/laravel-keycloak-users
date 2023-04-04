<?php

namespace berthott\KeycloakUsers\Facades;

use Illuminate\Support\Facades\Facade;

class KeycloakLog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'KeycloakLog';
    }
}
