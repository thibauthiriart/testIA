<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $department;
    protected $city;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les rôles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Créer un admin
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        // Créer un user normal
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        
        // Créer des données de test
        $this->department = Department::factory()->create();
        $this->city = City::factory()->create([
            'department_id' => $this->department->id
        ]);
    }

    /**
     * Test que les utilisateurs non connectés sont redirigés vers login
     */
    public function test_guest_cannot_access_protected_routes()
    {
        $routes = [
            ['method' => 'get', 'url' => '/dashboard'],
            ['method' => 'get', 'url' => '/cities'],
            ['method' => 'get', 'url' => '/departments'],
            ['method' => 'get', 'url' => '/users'],
            ['method' => 'get', 'url' => '/map'],
            ['method' => 'get', 'url' => '/account'],
        ];

        foreach ($routes as $route) {
            $response = $this->{$route['method']}($route['url']);
            $response->assertRedirect('/login');
        }
    }

    /**
     * Test que les admins peuvent accéder à toutes les routes
     */
    public function test_admin_can_access_all_routes()
    {
        // Routes GET
        $getRoutes = [
            '/dashboard' => 200,
            '/cities' => 200,
            '/departments' => 200,
            '/users' => 200,
            '/map' => 200,
            '/account' => 200,
            "/cities/{$this->city->id}" => 200,
            "/departments/{$this->department->id}" => 200,
        ];

        foreach ($getRoutes as $url => $expectedStatus) {
            $response = $this->actingAs($this->admin)->get($url);
            $response->assertStatus($expectedStatus);
        }
    }

    /**
     * Test que les users normaux ne peuvent pas accéder aux routes admin
     */
    public function test_user_cannot_access_admin_routes()
    {
        // Test GET /cities first
        $response = $this->actingAs($this->user)->get('/cities');
        
        // Debug: Check what roles the user has
        $this->assertFalse($this->user->hasRole('admin'));
        $this->assertTrue($this->user->hasRole('user'));
        
        $response->assertStatus(403);
        
        // Then test other routes
        $adminRoutes = [
            ['method' => 'get', 'url' => '/departments'],
            ['method' => 'get', 'url' => '/users'],
            ['method' => 'post', 'url' => '/cities'],
            ['method' => 'post', 'url' => '/departments'],
            ['method' => 'put', 'url' => "/cities/{$this->city->id}"],
            ['method' => 'put', 'url' => "/departments/{$this->department->id}"],
            ['method' => 'delete', 'url' => "/cities/{$this->city->id}"],
            ['method' => 'delete', 'url' => "/departments/{$this->department->id}"],
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($this->user)->{$route['method']}($route['url']);
            $response->assertStatus(403); // Forbidden
        }
    }

    /**
     * Test que les users peuvent accéder aux routes publiques
     */
    public function test_user_can_access_public_routes()
    {
        $publicRoutes = [
            '/dashboard' => 200,
            '/map' => 200,
            '/account' => 200,
        ];

        foreach ($publicRoutes as $url => $expectedStatus) {
            $response = $this->actingAs($this->user)->get($url);
            $response->assertStatus($expectedStatus);
        }
    }

    /**
     * Test CRUD complet pour les cities avec admin
     */
    public function test_admin_can_perform_full_crud_on_cities()
    {
        // Create
        $cityData = [
            'name' => 'New City',
            'postal_code' => '12345',
            'population' => 10000,
            'department_id' => $this->department->id
        ];
        
        $response = $this->actingAs($this->admin)->post('/cities', $cityData);
        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', $cityData);
        
        $newCity = City::where('name', 'New City')->first();
        
        // Read
        $response = $this->actingAs($this->admin)->get("/cities/{$newCity->id}");
        $response->assertStatus(200);
        $response->assertJson(['name' => 'New City']);
        
        // Update
        $updateData = [
            'name' => 'Updated City',
            'postal_code' => '54321',
            'population' => 20000,
            'department_id' => $this->department->id
        ];
        
        $response = $this->actingAs($this->admin)->put("/cities/{$newCity->id}", $updateData);
        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', array_merge(['id' => $newCity->id], $updateData));
        
        // Delete
        $response = $this->actingAs($this->admin)->delete("/cities/{$newCity->id}");
        $response->assertRedirect('/cities');
        $this->assertDatabaseMissing('cities', ['id' => $newCity->id]);
    }

    /**
     * Test que user ne peut pas faire de CRUD sur cities
     */
    public function test_user_cannot_perform_crud_on_cities()
    {
        // Create
        $cityData = [
            'name' => 'New City',
            'postal_code' => '12345',
            'population' => 10000,
            'department_id' => $this->department->id
        ];
        
        $response = $this->actingAs($this->user)->post('/cities', $cityData);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('cities', ['name' => 'New City']);
        
        // Read (API)
        $response = $this->actingAs($this->user)->get("/cities/{$this->city->id}");
        $response->assertStatus(403);
        
        // Update
        $response = $this->actingAs($this->user)->put("/cities/{$this->city->id}", [
            'name' => 'Updated City',
            'postal_code' => '54321',
            'population' => 20000,
            'department_id' => $this->department->id
        ]);
        $response->assertStatus(403);
        
        // Delete
        $response = $this->actingAs($this->user)->delete("/cities/{$this->city->id}");
        $response->assertStatus(403);
    }

    /**
     * Test gestion des utilisateurs (admin only)
     */
    public function test_admin_can_manage_users()
    {
        // List users
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
        
        // Update user role
        $response = $this->actingAs($this->admin)->put("/users/{$this->user->id}/role", [
            'role' => 'admin'
        ]);
        $response->assertRedirect('/users');
        $this->assertTrue($this->user->fresh()->hasRole('admin'));
        
        // Delete user
        $userToDelete = User::factory()->create();
        $response = $this->actingAs($this->admin)->delete("/users/{$userToDelete->id}");
        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    /**
     * Test que user ne peut pas gérer les utilisateurs
     */
    public function test_user_cannot_manage_users()
    {
        // List users
        $response = $this->actingAs($this->user)->get('/users');
        $response->assertStatus(403);
        
        // Update user role
        $anotherUser = User::factory()->create();
        $response = $this->actingAs($this->user)->put("/users/{$anotherUser->id}/role", [
            'role' => 'admin'
        ]);
        $response->assertStatus(403);
        
        // Delete user
        $response = $this->actingAs($this->user)->delete("/users/{$anotherUser->id}");
        $response->assertStatus(403);
    }

    /**
     * Test que l'admin ne peut pas se supprimer lui-même
     */
    public function test_admin_cannot_delete_self()
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id}");
        $response->assertRedirect('/users');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    /**
     * Test account management pour tous les utilisateurs connectés
     */
    public function test_authenticated_users_can_manage_own_account()
    {
        // Admin peut voir et modifier son compte
        $response = $this->actingAs($this->admin)->get('/account');
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->admin)->put('/account', [
            'first_name' => 'Updated',
            'last_name' => 'Admin',
            'email' => 'updated@admin.com'
        ]);
        $response->assertRedirect('/account');
        
        // User peut voir et modifier son compte
        $response = $this->actingAs($this->user)->get('/account');
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->user)->put('/account', [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'email' => 'updated@user.com'
        ]);
        $response->assertRedirect('/account');
    }

    /**
     * Test logout pour tous les utilisateurs
     */
    public function test_all_authenticated_users_can_logout()
    {
        // Admin
        $response = $this->actingAs($this->admin)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
        
        // User
        $response = $this->actingAs($this->user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test que la page 403 est retournée avec Inertia
     */
    public function test_forbidden_access_returns_inertia_403_page()
    {
        $response = $this->actingAs($this->user)->get('/cities');
        
        $response->assertStatus(403);
        $response->assertInertia(fn ($page) => 
            $page->component('Errors/403')
        );
    }
}