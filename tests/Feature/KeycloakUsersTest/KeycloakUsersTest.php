<?php

namespace berthott\KeycloakUsers\Tests\Feature\KeycloakUsersTest;

use berthott\KeycloakUsers\Facades\KeycloakUsers;
use berthott\KeycloakUsers\Mail\NewUserMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class KeycloakUsersTest extends KeycloakUsersTestCase
{
    public function test_user_routes(): void
    {
        $expectedRoutes = [
          'users.index',
          'users.store',
          'users.show',
          'users.update',
          'users.destroy',
          'users.current',
        ];
        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $registeredRoutes);
        }
    }
    
    public function test_user_table_creation(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasColumns('users', [
            'id', 'keycloak_id', 'username', 'firstName', 'lastName', 'email', 'created_at', 'updated_at',
        ]));
    }

    /**
     * For this to work, you need to add to the keycloak realm default role
     * the realm-management client roles 'manage-users', 'query-users' and 'view-users'.
     * Select Realm -> Roles -> Default Roles -> Client Roles realm-management.
     */
    public function test_user_sync(): void
    {
        // automatic user sync on init
        KeycloakUsers::init();
        $this->assertDatabaseHas('users', [
            'username' => 'test.test',
        ]);
    }

    /**
     * We are testing the whole feature in one method, to leave a clean state inside keycloak.
     */
    public function test_keycloak_interaction(): void
    {
        // creation
        Mail::fake();
        KeycloakUsers::init();
        $this->assertDatabaseMissing('users', ['username' => 'testfirst.testlast']);
        $this->assertDatabaseCount('users', 1);
        User::create([
          'firstName' => 'Testfirst',
          'lastName' => 'Testlast',
          'email' => 'testfirst.testlast@test.com'
        ]);
        Mail::assertSent(NewUserMail::class);
        $this->assertDatabaseHas('users', ['username' => 'testfirst.testlast']);
        DB::table('users')->truncate();
        $this->assertDatabaseMissing('users', ['username' => 'testfirst.testlast']);
        KeycloakUsers::init();
        $this->assertDatabaseHas('users', ['username' => 'testfirst.testlast']);
        $this->assertDatabaseCount('users', 2);

        // update
        $updateUser = User::where('email', 'testfirst.testlast@test.com')->first();
        $updateUser->firstName = 'changed';
        $updateUser->save();
        $this->assertDatabaseHas('users', ['username' => 'changed.testlast']);
        DB::table('users')->truncate();
        $this->assertDatabaseMissing('users', ['username' => 'changed.testlast']);
        KeycloakUsers::init();
        $this->assertDatabaseHas('users', ['username' => 'changed.testlast']);
        $this->assertDatabaseCount('users', 2);

        // deletion
        $deleteUser = User::where('email', 'testfirst.testlast@test.com')->first();
        $deleteUser->delete();
        $this->assertDatabaseMissing('users', ['username' => 'changed.testlast']);
        KeycloakUsers::init();
        $this->assertDatabaseMissing('users', ['username' => 'changed.testlast']);
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * We are testing the whole feature in one method, to leave a clean state inside keycloak.
     */
    public function test_keycloak_interaction_routes(): void
    {
        // creation
        Mail::fake();
        KeycloakUsers::init();
        $this->assertDatabaseMissing('users', ['username' => 'testfirst.testlast']);
        $this->assertDatabaseCount('users', 1);
        $id = $this->post(route('users.store'), [
            'firstName' => 'Testfirst',
            'lastName' => 'Testlast',
            'email' => 'testfirst.testlast@test.com'
        ])
            ->assertStatus(201)
            ->json()['id'];
        Mail::assertSent(NewUserMail::class);
        $this->assertDatabaseHas('users', ['username' => 'testfirst.testlast']);
        $this->assertDatabaseCount('users', 2);

        // update
        $this->put(route('users.update', ['user' => $id]), [
            'firstName' => 'changed',
            'lastName' => 'Testlast',
            'email' => 'testfirst.testlast@test.com'
        ])
            ->assertStatus(200);
        $this->assertDatabaseHas('users', ['username' => 'changed.testlast']);
        $this->assertDatabaseCount('users', 2);

        // deletion
        $this->delete(route('users.destroy', ['user' => $id]))
            ->assertStatus(200);
        $this->assertDatabaseMissing('users', ['username' => 'changed.testlast']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_current_user(): void
    {
        KeycloakUsers::init();
        $user = User::first();
        $attributes = array_intersect_key($user->getAttributes(), array_fill_keys([
          'username',
          'firstName',
          'lastName',
          'email',
        ], ''));
        $this->actingAs($user);
        $this->get(route('users.current'))
            ->assertStatus(200)
            ->assertJson($attributes);
    }

    /**
     * We are testing the whole feature in one method, to leave a clean state inside keycloak.
     */
    public function test_keycloak_exception(): void
    {
        // creation
        Mail::fake();
        KeycloakUsers::init();
        $this->assertDatabaseMissing('users', ['username' => 'testfirst.testlast']);
        $this->assertDatabaseCount('users', 1);
        $id = $this->post(route('users.store'), [
            'firstName' => 'Testfirst',
            'lastName' => 'Testlast',
            'email' => 'testfirst.testlast@test.com'
        ])
            ->assertStatus(201)
            ->json()['id'];
        $this->assertDatabaseHas('users', ['username' => 'testfirst.testlast']);
        $this->assertDatabaseCount('users', 2);

        // create user with same name, different mail address
        $this->post(route('users.store'), [
            'firstName' => 'Testfirst',
            'lastName' => 'Testlast',
            'email' => 'test.test@test.com'
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => 'User exists with same username'
            ]);

        // deletion
        $this->delete(route('users.destroy', ['user' => $id]))
            ->assertStatus(200);
        $this->assertDatabaseMissing('users', ['username' => 'changed.testlast']);
        $this->assertDatabaseCount('users', 1);
    }
}
