<?php

namespace berthott\KeycloakUsers\Services;

use berthott\KeycloakUsers\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use LaravelKeycloakAdmin\Facades\KeycloakAdmin;

class KeycloakUsersService {

  /**
   * The Constructor
   *
   * @return void
   */
  public function __construct() {
  }

  /**
   * The Constructor
   *
   * @return void
   */
  public function init() {
    if (!App::runningInConsole() && Schema::hasTable('users')) {
      $this->syncUsers();
    }
  }

  /**
   * Sync the database with keycloaks users
   *
   * @return void
   */
  protected function syncUsers()
  {
    // delete users deleted in keycloak
    $keycloakUsers = collect(KeycloakAdmin::user()->all());
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
    $fillableFields = (new User)->fillable;
    foreach($keycloakUsers as $keycloakUser) {
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