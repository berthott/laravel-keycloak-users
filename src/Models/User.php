<?php

namespace berthott\KeycloakUsers\Models;


use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Crudable;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

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

    /**
     * @return array
     */
    public static function rules(): array {
        return [
            'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'email' => 'required|email',
        ];
    }
}
