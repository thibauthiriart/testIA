<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;
    
    protected $admin;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les rôles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Créer un utilisateur admin
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_index_returns_departments_view()
    {
        // Créer quelques départements de test
        Department::factory()->create(['name' => 'Ain', 'code' => '01']);
        Department::factory()->create(['name' => 'Aisne', 'code' => '02']);
        
        $response = $this->actingAs($this->admin)->get('/departments');
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Departments/Index')
                ->has('departments.data', 2)
        );
    }

    public function test_store_creates_new_department()
    {
        $departmentData = [
            'name' => 'Test Department',
            'code' => '99'
        ];

        $response = $this->actingAs($this->admin)->post('/departments', $departmentData);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', $departmentData);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)->post('/departments', []);

        $response->assertSessionHasErrors(['name', 'code']);
    }

    public function test_store_validates_unique_code()
    {
        Department::factory()->create(['code' => '01']);

        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Test Department',
            'code' => '01'
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    public function test_show_returns_department()
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->admin)->get("/departments/{$department->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $department->id,
            'name' => $department->name,
            'code' => $department->code
        ]);
    }

    public function test_update_modifies_department()
    {
        $department = Department::factory()->create();
        $updateData = [
            'name' => 'Updated Department',
            'code' => '98'
        ];

        $response = $this->actingAs($this->admin)->put("/departments/{$department->id}", $updateData);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', array_merge(['id' => $department->id], $updateData));
    }

    public function test_destroy_deletes_department()
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/departments/{$department->id}");

        $response->assertRedirect('/departments');
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }
}