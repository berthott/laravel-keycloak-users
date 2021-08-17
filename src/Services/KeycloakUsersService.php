<?php

namespace berthott\KeycloakUsers\Services;

use berthott\KeycloakUsers\Models\User;
use Mnikoei\Facades\KeycloakAdmin;

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
    $this->loadMissingUsers();
  }

  /**
   * Sync the database with keycloaks users
   *
   * @return void
   */
  protected function loadMissingUsers()
  {
    $fillableFields = (new User)->fillable;
    foreach(KeycloakAdmin::user()->all() as $keycloakUser) {
      User::firstOrCreate(array_intersect_key($keycloakUser, array_fill_keys($fillableFields, '')));
    }
  }
}