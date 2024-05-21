<?php

namespace VoyagerInc\PermissionRole\Tests;

use \Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionRoleTest extends BaseTest
{
    use RefreshDatabase;

     /**
     * Test a user with admin role can access admin route.
     */
    public function test_admin_can_access_admin_route(): void
    {
        $admin = \App\Models\User::factory()->create([
            'role' => 'admin',
        ]);

        // authenticate
        $this->actingAs($admin);

        $response = $this->get('/permission-role/admin');

        $response->assertStatus(200);
    }

     /**
     * Test a user with user role cannot access admin route.
     */
    public function test_user_cannot_access_admin_route(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'user',
        ]);

        // authenticate
        $this->actingAs($user);

        $response = $this->get('/permission-role/admin');

        $response->assertStatus(403);
    }

    /**
     * Test guest (unauthenticated user) cannot access admin route.
     */
    public function test_guest_cannot_access_admin_route(): void
    {
        $response = $this->get('/permission-role/admin');

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Unauthorized']);
    }

    /**
     * Test guest (unauthenticated user) cannot access user route.
     */
    public function test_guest_cannot_access_user_route(): void
    {
        $response = $this->get('/permission-role/user');

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Unauthorized']);
    }
}