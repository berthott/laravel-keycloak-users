<?php

namespace berthott\KeycloakUsers\Observers;

use berthott\KeycloakUsers\Models\User;
use Mnikoei\Facades\KeycloakAdmin;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
      /* KeycloakAdmin::user()->create([
      
        'body' => [  // https://www.keycloak.org/docs-api/7.0/rest-api/index.html#_userrepresentation
          'username' => "$user->firstName.$user->lastName",
          'firstName' => $user->fistName,
          'lastName' => $user->lastName,
        ]
   
      ]); */
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "forceDeleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}