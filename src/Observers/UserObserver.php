<?php

namespace berthott\KeycloakUsers\Observers;

use Closure;
use berthott\KeycloakUsers\Exceptions\KeycloakUsersException;
use berthott\KeycloakUsers\Models\User;
use GuzzleHttp\Exception\ClientException;
use Mnikoei\Facades\KeycloakAdmin;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Create the user in Keycloak, and use the returning ID to 
     * store it to the database.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
      $user->username = "$user->firstName.$user->lastName";
      $this->captureExceptions(function () use ($user) {
        $createdUser = KeycloakAdmin::user()->create([
          'body' => [  // https://www.keycloak.org/docs-api/14.0/rest-api/index.html#_userrepresentation
            'username' => $user->username,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
          ]
        ]);
        $user->id = $createdUser['id'];
      });
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

    /**
     * Catch Guzzle Exception and throw Laravel Exception
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    private function captureExceptions(Closure $f)
    {
        try {
          $f();
        } catch (ClientException $e) {
          throw new KeycloakUsersException($e);
        }
    }
}