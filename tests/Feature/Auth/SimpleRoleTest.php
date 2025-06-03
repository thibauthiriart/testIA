<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class SimpleRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_blocks_user_from_cities()
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Create user with 'user' role
        $user = User::factory()->create();
        $user->assignRole('user');
        
        // Try to access cities
        $response = $this->actingAs($user)->get('/cities');
        
        // Should be forbidden
        $response->assertStatus(403);
    }
    
    public function test_middleware_allows_admin_to_cities()
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Create admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Try to access cities
        $response = $this->actingAs($admin)->get('/cities');
        
        // Should be allowed
        $response->assertStatus(200);
    }
}