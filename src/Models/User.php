<?php

namespace berthott\KeycloakUsers\Models;


use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

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
     * @param  mixed  $id
     * @return array
     */
    public static function rules($id): array {
        return [
            'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'email' => [
                'required', 'email',
                ($id ? Rule::unique('users')->ignore($id) : 'unique:users')
            ],
        ];
    }
}
