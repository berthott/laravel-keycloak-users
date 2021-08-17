<?php

namespace berthott\KeycloakUsers\Facades;

use Illuminate\Support\Facades\Facade;


class KeycloakUsers extends Facade
{
    protected static function getFacadeAccessor(){

        return 'KeycloakUsers';

    }
}