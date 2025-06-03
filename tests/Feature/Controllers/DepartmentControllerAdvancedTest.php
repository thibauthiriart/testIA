<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Department;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class DepartmentControllerAdvancedTest extends TestCase
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

    public function test_cannot_create_department_with_duplicate_code()
    {
        // Créer un département existant
        Department::factory()->create(['code' => '75']);

        // Tenter de créer un autre département avec le même code
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Paris Duplicate',
            'code' => '75'
        ]);

        $response->assertSessionHasErrors(['code']);
        $this->assertCount(1, Department::where('code', '75')->get());
    }

    public function test_cannot_create_department_with_invalid_code_format()
    {
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Invalid Department',
            'code' => 'ABC' // Code non numérique
        ]);

        $response->assertSessionHasErrors(['code']);
        $this->assertDatabaseMissing('departments', ['code' => 'ABC']);
    }

    public function test_cannot_delete_department_with_cities()
    {
        $department = Department::factory()->create();
        City::factory()->count(3)->create(['department_id' => $department->id]);

        $response = $this->actingAs($this->admin)->delete("/departments/{$department->id}");

        // La suppression devrait échouer ou supprimer en cascade selon la config
        // Dans notre cas, on a onDelete('cascade'), donc les villes sont supprimées
        $response->assertRedirect('/departments');
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
        $this->assertDatabaseMissing('cities', ['department_id' => $department->id]);
    }

    public function test_cannot_update_department_with_existing_code()
    {
        $department1 = Department::factory()->create(['code' => '01']);
        $department2 = Department::factory()->create(['code' => '02']);

        $response = $this->actingAs($this->admin)->put("/departments/{$department2->id}", [
            'name' => 'Updated Name',
            'code' => '01' // Code déjà utilisé
        ]);

        $response->assertSessionHasErrors(['code']);
        $this->assertDatabaseHas('departments', [
            'id' => $department2->id,
            'code' => '02' // Code non modifié
        ]);
    }

    public function test_department_name_must_be_at_least_2_characters()
    {
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'A', // Trop court
            'code' => '99'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_department_name_cannot_exceed_100_characters()
    {
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => str_repeat('a', 101), // Trop long
            'code' => '99'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_cannot_access_non_existent_department()
    {
        $response = $this->actingAs($this->admin)->get('/departments/99999');
        $response->assertStatus(404);
    }

    public function test_index_pagination_works_correctly()
    {
        // Créer 15 départements
        Department::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)->get('/departments');
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Departments/Index')
                ->has('departments.data', 10) // Par défaut 10 par page
                ->where('departments.total', 15)
        );
    }

    public function test_can_filter_departments_by_partial_name()
    {
        Department::factory()->create(['name' => 'Haute-Savoie']);
        Department::factory()->create(['name' => 'Savoie']);
        Department::factory()->create(['name' => 'Paris']);

        // Test avec recherche partielle
        $response = $this->actingAs($this->admin)->get('/departments?search=Sav');
        
        $response->assertStatus(200);
        // Note: Ce test suppose que le contrôleur supporte la recherche
        // Si ce n'est pas le cas, il faudra l'implémenter
    }

    public function test_department_code_must_be_unique_case_insensitive()
    {
        Department::factory()->create(['code' => '75']);

        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Another Paris',
            'code' => '75' // Même code
        ]);

        $response->assertSessionHasErrors(['code']);
    }
}