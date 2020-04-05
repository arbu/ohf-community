<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class UserRoleApiTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexWithoutAuthentication()
    {
        $user = factory(User::class)->create();

        $response = $this->getJson('api/users/' . $user->id . '/roles', []);

        $this->assertGuest();
        $response->assertUnauthorized();
    }

    public function testIndexWithoutAuthorization()
    {
        $user = factory(User::class)->create();

        $authUser = factory(User::class)->make();

        $response = $this->actingAs($authUser)
            ->getJson('api/users/' . $user->id . '/roles', []);

        $this->assertAuthenticated();
        $response->assertForbidden();
    }

    public function testIndexWithNonExistingUser()
    {
        Config::set('app.debug', false);

        $authUser = $this->makeUserWithPermission('app.usermgmt.view');

        $response = $this->actingAs($authUser)
            ->getJson('api/users/' . 1234 . '/roles', []);

        $this->assertAuthenticated();
        $response->assertNotFound()
            ->assertExactJson([
                'message' => 'No query results for model [App\\User] 1234',
            ]);
    }

    public function testIndexWithoutRecords()
    {
        $user = factory(User::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.view');

        $response = $this->actingAs($authUser)
            ->getJson('api/users/' . $user->id . '/roles', []);

        $this->assertAuthenticated();
        $response->assertOk()
            ->assertExactJson([
                'data' => [ ],
            ]);
    }

    public function testIndexWithRecords()
    {
        $user = factory(User::class)->create();
        $role1 = factory(Role::class)->create([
            'name' => 'Role A',
        ]);
        $role2 = factory(Role::class)->create([
            'name' => 'Role B',
        ]);
        $user->roles()->sync([$role1->id, $role2->id]);

        $authUser = $this->makeUserWithPermission('app.usermgmt.view');

        $response = $this->actingAs($authUser)
            ->getJson('api/users/' . $user->id . '/roles', []);

        $this->assertAuthenticated();
        $response->assertOk()
            ->assertExactJson([
                'data' => [
                    [
                        'id' => $role1->id,
                        'name' => $role1->name,
                        'url' => route('api.roles.show', $role1),
                        'users_count' => 1,
                        'admins_count' => 0,
                        'user_is_admin' => false,
                        'created_at' => $role1->created_at,
                        'updated_at' => $role1->updated_at,
                    ],
                    [
                        'id' => $role2->id,
                        'name' => $role2->name,
                        'url' => route('api.roles.show', $role2),
                        'users_count' => 1,
                        'admins_count' => 0,
                        'user_is_admin' => false,
                        'created_at' => $role2->created_at,
                        'updated_at' => $role2->updated_at,
                    ]
                ],
            ]);
    }

    public function testIndexWithFilteredRecords()
    {
        $role1 = factory(Role::class)->create([
            'name' => 'Accountant',
        ]);
        $role2 = factory(Role::class)->create([
            'name' => 'Security Officer',
        ]);
        $role3 = factory(Role::class)->create([
            'name' => 'Finance Officer',
        ]);
        $role4 = factory(Role::class)->create([
            'name' => 'Security Guard',
        ]);

        $authUser = $this->makeUserWithPermission('app.usermgmt.view');

        $response = $this->actingAs($authUser)
            ->getJson('api/roles?filter=officer', []);

        $this->assertAuthenticated();
        $response->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $role3->id,
                        'name' => $role3->name,
                    ],
                    [
                        'id' => $role2->id,
                        'name' => $role2->name,
                    ],
                ],
            ])
            ->assertJsonMissing([
                'data' => [
                    [
                        'id' => $role1->id,
                        'name' => $role1->name,
                    ],
                    [
                        'id' => $role4->id,
                        'name' => $role4->name,
                    ],
                ],
            ]);
    }

    public function testStoreWithInsufficientPermissions()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.view');

        $response = $this->actingAs($authUser)
            ->postJson('api/users/' . $user->id . '/roles', [
                'id' => $role->id,
            ]);

        $this->assertAuthenticated();
        $response->assertForbidden();
    }

    public function testStoreWithNonExistingUser()
    {
        $role = factory(Role::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.users.manage');

        $response = $this->actingAs($authUser)
            ->postJson('api/users/' . 1234 . '/roles', [
                'id' => $role->id,
            ]);

        $this->assertAuthenticated();
        $response->assertNotFound();
    }

    public function testStoreWithNonExistingRole()
    {
        $user = factory(User::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.users.manage');

        $response = $this->actingAs($authUser)
            ->postJson('api/users/' . $user->id . '/roles', [
                'id' => 1234,
            ]);

        $this->assertAuthenticated();
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['id']);
    }

    public function testStoreWithValidRole()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.users.manage');

        $response = $this->actingAs($authUser)
            ->postJson('api/users/' . $user->id . '/roles', [
                'id' => $role->id,
            ]);

        $this->assertAuthenticated();
        $response->assertCreated()
            ->assertExactJson([
                'message' => __('app.role_added'),
            ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    public function testStoreWithAlreadyAttachedRole()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();
        $user->roles()->attach($role);

        $authUser = $this->makeUserWithPermission('app.usermgmt.users.manage');

        $response = $this->actingAs($authUser)
            ->postJson('api/users/' . $user->id . '/roles', [
                'id' => $role->id,
            ]);

        $this->assertAuthenticated();
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['id']);
    }

    public function testDestroyWithInsufficientPermissions()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.view');

        $response = $this->actingAs($authUser)
            ->deleteJson('api/users/' . $user->id . '/roles/' . $role->id, [ ]);

        $this->assertAuthenticated();
        $response->assertForbidden();
    }

    public function testDestroyWithNonExistingUser()
    {
        $role = factory(Role::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.users.manage');

        $response = $this->actingAs($authUser)
            ->deleteJson('api/users/' . 1234 . '/roles/' . $role->id, [ ]);

        $this->assertAuthenticated();
        $response->assertNotFound();
    }

    public function testDestroyWithNonExistingRole()
    {
        $user = factory(User::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.users.manage');

        $response = $this->actingAs($authUser)
            ->deleteJson('api/users/' . $user->id . '/roles/' . 1234, [ ]);

        $this->assertAuthenticated();
        $response->assertNotFound();
    }

    public function testDestroyWithoutAttachedRole()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();

        $authUser = $this->makeUserWithPermission('app.usermgmt.users.manage');

        $response = $this->actingAs($authUser)
            ->deleteJson('api/users/' . $user->id . '/roles/' . $role->id, [ ]);

        $this->assertAuthenticated();
        $response->assertNotFound();
    }

    public function testDestroyWitAttachedRole()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();
        $user->roles()->attach($role);

        $authUser = $this->makeUserWithPermission('app.usermgmt.users.manage');

        $response = $this->actingAs($authUser)
            ->deleteJson('api/users/' . $user->id . '/roles/' . $role->id, [ ]);

        $this->assertAuthenticated();
        $response->assertOk()
            ->assertExactJson([
                'message' => __('app.role_deleted'),
            ]);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

}
