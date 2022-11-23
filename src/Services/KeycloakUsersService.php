<?php

namespace berthott\KeycloakUsers\Services;

use berthott\KeycloakUsers\Exceptions\KeycloakNoUsersException;
use berthott\KeycloakUsers\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use LaravelKeycloakAdmin\Facades\KeycloakAdmin;

class KeycloakUsersService
{
    /**
     * The Constructor.
     */
    public function __construct()
    {
    }

    /**
     * The Constructor.
     */
    public function init(): void
    {
        if ((!App::runningInConsole() || App::runningUnitTests()) && Schema::hasTable('users')) {
            $this->syncUsers();
        }
    }

    /**
     * Sync the database with keycloaks users.
     */
    protected function syncUsers(): void
    {
        // delete users deleted in keycloak
        $keycloakUsers = collect(KeycloakAdmin::user()->all());
        if (count($keycloakUsers) === 1 && $keycloakUsers[0] === true) {
            // KeycloakAdmin returns true instead of array when empty
            throw new KeycloakNoUsersException();
        }
        foreach (User::all() as $user) {
            if ($keycloakUsers->contains(function ($keycloakUser) use ($user) {
                return $keycloakUser['id'] == $user->keycloak_id;
            })) {
                continue;
            }
            User::withoutEvents(function () use ($user) {
                $user->delete();
            });
        }

        // create or update users created or updated in keycloak
        $fillableFields = (new User())->fillable;
        foreach ($keycloakUsers as $keycloakUser) {
            User::withoutEvents(function () use ($keycloakUser, $fillableFields) {
                User::updateOrCreate(
                    ['keycloak_id' => $keycloakUser['id']],
                    array_merge(
                        ['keycloak_id' => $keycloakUser['id']],
                        array_intersect_key(
                            $keycloakUser,
                            array_fill_keys($fillableFields, '')
                        )
                    )
                );
            });
        }
    }
}
