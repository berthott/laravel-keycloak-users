<?php

namespace berthott\KeycloakUsers\Services;

use berthott\KeycloakUsers\Exceptions\KeycloakNoUsersException;
use berthott\KeycloakUsers\Facades\KeycloakLog;
use berthott\KeycloakUsers\Models\User;
use Illuminate\Support\Collection;
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
        // paginate over keycloakusers
        $keycloakUsers = collect();
        $end = false;
        while(!$end) {
            $newUsers = collect(KeycloakAdmin::user()->all([
                'query' => [
                    'first' => $keycloakUsers->count(),
                ]
            ]));
            $end = $this->noUsersReturned($newUsers);
            if (!$end) {
                $keycloakUsers->push(...$newUsers);
            }
        }
        if ($this->noUsersReturned($keycloakUsers)) {
            KeycloakLog::log('No Keycloak Users to sync');
            throw new KeycloakNoUsersException();
        }
        $ids = $keycloakUsers->pluck('id')->join(', ');
        KeycloakLog::log("Syncing Keycloak Users (count: {$keycloakUsers->count()}, keycloak_ids: {$ids}...");

        // delete users deleted in keycloak
        foreach (User::all() as $user) {
            if ($keycloakUsers->contains(function ($keycloakUser) use ($user) {
                return $keycloakUser['id'] == $user->keycloak_id;
            })) {
                continue;
            }
            User::withoutEvents(function () use ($user) {
                $user->delete();
                KeycloakLog::log("Syncing Keycloak: User Deleted (id: {$user->id}, keycloak_id: {$user->keycloak_id}");
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

    /**
     * Has Keycloak returned users
     */
    private function noUsersReturned(Collection $collection): bool
    {
        // KeycloakAdmin returns true instead of array when empty
        return $collection->count() === 1 && $collection[0] === true;
    }
}
