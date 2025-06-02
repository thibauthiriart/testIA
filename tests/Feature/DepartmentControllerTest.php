<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_departments_view()
    {
        // Créer quelques départements de test
        Department::factory()->create(['nom' => 'Ain', 'code' => '01']);
        Department::factory()->create(['nom' => 'Aisne', 'code' => '02']);
        
        $response = $this->get('/departments');
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Departments/Index')
                ->has('departments.data', 2)
        );
    }

    public function test_store_creates_new_department()
    {
        $departmentData = [
            'nom' => 'Test Department',
            'code' => '99'
        ];

        $response = $this->post('/departments', $departmentData);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', $departmentData);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post('/departments', []);

        $response->assertSessionHasErrors(['nom', 'code']);
    }

    public function test_store_validates_unique_code()
    {
        Department::factory()->create(['code' => '01']);

        $response = $this->post('/departments', [
            'nom' => 'Test Department',
            'code' => '01'
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    public function test_show_returns_department()
    {
        $department = Department::factory()->create();

        $response = $this->get("/departments/{$department->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $department->id,
            'nom' => $department->nom,
            'code' => $department->code
        ]);
    }

    public function test_update_modifies_department()
    {
        $department = Department::factory()->create();
        $updateData = [
            'nom' => 'Updated Department',
            'code' => '98'
        ];

        $response = $this->put("/departments/{$department->id}", $updateData);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', array_merge(['id' => $department->id], $updateData));
    }

    public function test_destroy_deletes_department()
    {
        $department = Department::factory()->create();

        $response = $this->delete("/departments/{$department->id}");

        $response->assertRedirect('/departments');
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }
}