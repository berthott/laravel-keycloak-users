<?php

namespace berthott\KeycloakUsers\Observers;

use berthott\KeycloakUsers\Exceptions\KeycloakUsersException;
use berthott\KeycloakUsers\Facades\KeycloakLog;
use berthott\KeycloakUsers\Mail\NewUserMail;
use berthott\KeycloakUsers\Models\User;
use Closure;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use LaravelKeycloakAdmin\Facades\KeycloakAdmin;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Create the user in Keycloak, and use the returning ID to
     * store it to the database.
     */
    public function creating(User $user): void
    {
        $user->username = $this->username($user);
        $this->captureExceptions(function () use ($user) {
            KeycloakLog::log('Creating Keycloak User...');
            $password = Str::random(12);
            $createdUser = KeycloakAdmin::user()->create([
              'body' => [  // https://www.keycloak.org/docs-api/14.0/rest-api/index.html#_userrepresentation
                'username' => $user->username,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
                'enabled' => true,
              ],
            ]);
            KeycloakAdmin::user()->setTemporaryPassword([
              'id' => $createdUser['id'],
              'body' => [  // https://www.keycloak.org/docs-api/14.0/rest-api/index.html#_userrepresentation
                'type' => 'password',
                'value' => $password,
                'temporary' => true,
              ],
            ]);
            $user->keycloak_id = $createdUser['id'];
            KeycloakLog::log("Keycloak User Created (id: {$user->id}, keycloak_id: {$user->keycloak_id})");
            Mail::to($user)->send(new NewUserMail($user, $password));
        });
    }

    /**
     * Handle the User "updated" event.
     */
    public function updating(User $user): void
    {
        $user->username = $this->username($user);
        $this->captureExceptions(function () use ($user) {
            KeycloakLog::log('Updating Keycloak User...');
            KeycloakAdmin::user()->update([
              'id' => $user->keycloak_id,
              'body' => [  // https://www.keycloak.org/docs-api/14.0/rest-api/index.html#_userrepresentation
                'username' => $user->username,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
              ],
            ]);
            KeycloakLog::log("Keycloak User Updated (id: {$user->id}, keycloak_id: {$user->keycloak_id})");
        });
    }

    /**
     * Handle the User "deleted" event.
     * Delete the user inside Keycloak.
     */
    public function deleting(User $user): void
    {
        $this->captureExceptions(function () use ($user) {
            KeycloakLog::log('Deleting Keycloak User...');
            KeycloakAdmin::user()->delete([
              'id' => $user->keycloak_id,
            ]);
            KeycloakLog::log("Keycloak User Deleted (id: {$user->id}, keycloak_id: {$user->keycloak_id})");
        });
    }

    /**
     * Catch Guzzle Exception and throw Laravel Exception.
     *
     * @throws KeycloakUsersException
     */
    private function captureExceptions(Closure $func): void
    {
        try {
            $func();
        } catch (RequestException $e) {

            $error = json_decode($e->getResponse()->getBody()->getContents())->errorMessage;
            KeycloakLog::log("Keycloak Exception: {$error}");
            throw new KeycloakUsersException($e);
        }
    }

    /**
     * Get the username.
     */
    private function username(User $user): string
    {
        return Str::lower("$user->firstName.$user->lastName");
    }
}
