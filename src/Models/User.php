<?php

namespace berthott\KeycloakUsers\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'firstName',
        'lastName',
        'username',
        'email',
    ];
}
