<?php

namespace berthott\KeycloakUsers\Observers;

use Closure;
use berthott\KeycloakUsers\Exceptions\KeycloakUsersException;
use berthott\KeycloakUsers\Mail\NewUserMail;
use berthott\KeycloakUsers\Models\User;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use Mnikoei\Facades\KeycloakAdmin;
use Illuminate\Support\Str;

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
      $user->username = $this->username($user);
      $this->captureExceptions(function () use ($user) {
        $password = Str::random(12);;
        $createdUser = KeycloakAdmin::user()->create([
          'body' => [  // https://www.keycloak.org/docs-api/14.0/rest-api/index.html#_userrepresentation
            'username' => $user->username,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
            'enabled' => true,
          ]
        ]);
        KeycloakAdmin::user()->setTemporaryPassword([
          'id' => $createdUser['id'],
          'body' => [  // https://www.keycloak.org/docs-api/14.0/rest-api/index.html#_userrepresentation
            'type' => 'password',
            'value' => $password,
            'temporary' => true,
          ]
        ]);
        $user->id = $createdUser['id'];
        Mail::to($user)->send(new NewUserMail($user, $password));
      });
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updating(User $user)
    {
      $user->username = $this->username($user);
      $this->captureExceptions(function () use ($user) {
        KeycloakAdmin::user()->update([
          'id' => $user->id,
          'body' => [  // https://www.keycloak.org/docs-api/14.0/rest-api/index.html#_userrepresentation
            'username' => $user->username,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
          ]
        ]);
      });
    }

    /**
     * Handle the User "deleted" event.
     * Delete the user inside Keycloak.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
      $this->captureExceptions(function () use ($user) {
        KeycloakAdmin::user()->delete([
          'id' => $user->id
        ]);
      });
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
        } catch (RequestException $e) {
          throw new KeycloakUsersException($e);
        }
    }

    /**
     * Get the username
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    private function username(User $user): string
    {
      return Str::lower("$user->firstName.$user->lastName");
    }
}