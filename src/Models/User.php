<?php

namespace berthott\KeycloakUsers\Models;

use berthott\Crudable\Models\Traits\Crudable;
use berthott\KeycloakUsers\Observers\UserObserver;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Validation\Rule;

/** 
 * User model implementation.
 */
class User extends Authenticatable
{
    use Crudable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    public $fillable = [
        'keycloak_id',
        'firstName',
        'lastName',
        'username',
        'email',
    ];

    /**
     * Validation rules
     * 
     * @return string[]
     */
    public static function rules(mixed $id): array
    {
        return [
            'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'email' => [
                'required', 'email',
                ($id ? Rule::unique('users')->ignore($id) : 'unique:users'),
            ],
            'keycloak_id' => 'nullable',
            'username' => 'nullable',
        ];
    }

    /**
     * Bootstrap services.
     */
    protected static function boot(): void
    {
        parent::boot();
        // observe user
        static::observe(UserObserver::class);
    }
}
