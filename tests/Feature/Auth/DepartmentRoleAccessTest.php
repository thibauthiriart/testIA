<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class DepartmentRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $department;

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

        // Créer un département de test
        $this->department = Department::factory()->create();
    }

    /**
     * Test CRUD complet pour les departments avec admin
     */
    public function test_admin_can_perform_full_crud_on_departments()
    {
        // Create
        $departmentData = [
            'name' => 'New Department',
            'code' => '99'
        ];

        $response = $this->actingAs($this->admin)->post('/departments', $departmentData);
        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', $departmentData);

        $newDept = Department::where('name', 'New Department')->first();

        // Read
        $response = $this->actingAs($this->admin)->get("/departments/{$newDept->id}");
        $response->assertStatus(200);
        $response->assertJson(['name' => 'New Department']);

        // Update
        $updateData = [
            'name' => 'Updated Department',
            'code' => '98'
        ];

        $response = $this->actingAs($this->admin)->put("/departments/{$newDept->id}", $updateData);
        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', array_merge(['id' => $newDept->id], $updateData));

        // Delete
        $response = $this->actingAs($this->admin)->delete("/departments/{$newDept->id}");
        $response->assertRedirect('/departments');
        $this->assertDatabaseMissing('departments', ['id' => $newDept->id]);
    }

    /**
     * Test que user ne peut pas faire de CRUD sur departments
     */
    public function test_user_cannot_perform_crud_on_departments()
    {
        // Create
        $departmentData = [
            'name' => 'New Department',
            'code' => '99'
        ];

        $response = $this->actingAs($this->user)->post('/departments', $departmentData);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('departments', ['name' => 'New Department']);

        // Read (API)
        $response = $this->actingAs($this->user)->get("/departments/{$this->department->id}");
        $response->assertStatus(403);

        // Update
        $response = $this->actingAs($this->user)->put("/departments/{$this->department->id}", [
            'name' => 'Updated Department',
            'code' => '98'
        ]);
        $response->assertStatus(403);

        // Delete
        $response = $this->actingAs($this->user)->delete("/departments/{$this->department->id}");
        $response->assertStatus(403);
    }

    /**
     * Test suppression en cascade des villes
     */
    public function test_admin_deleting_department_cascades_to_cities()
    {
        // Créer des villes dans le département
        $cities = City::factory()->count(3)->create([
            'department_id' => $this->department->id
        ]);

        $cityIds = $cities->pluck('id')->toArray();

        // Vérifier que les villes existent
        foreach ($cityIds as $id) {
            $this->assertDatabaseHas('cities', ['id' => $id]);
        }

        // Supprimer le département
        $response = $this->actingAs($this->admin)->delete("/departments/{$this->department->id}");
        $response->assertRedirect('/departments');

        // Vérifier que le département et ses villes sont supprimés
        $this->assertDatabaseMissing('departments', ['id' => $this->department->id]);
        foreach ($cityIds as $id) {
            $this->assertDatabaseMissing('cities', ['id' => $id]);
        }
    }

    /**
     * Test que user ne peut pas supprimer un département avec des villes
     */
    public function test_user_cannot_delete_department_with_cities()
    {
        // Créer des villes dans le département
        City::factory()->count(3)->create([
            'department_id' => $this->department->id
        ]);

        // Tenter de supprimer
        $response = $this->actingAs($this->user)->delete("/departments/{$this->department->id}");
        $response->assertStatus(403);

        // Vérifier que le département existe toujours
        $this->assertDatabaseHas('departments', ['id' => $this->department->id]);
    }

    /**
     * Test que user ne peut pas accéder aux filtres
     */
    public function test_user_cannot_access_department_filters()
    {
        $response = $this->actingAs($this->user)->get('/departments?search=test');
        $response->assertStatus(403);
    }

    /**
     * Test validation des départements pour admin
     */
    public function test_admin_department_validation_works()
    {
        // Code déjà existant
        Department::factory()->create(['code' => '75']);

        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Paris 2',
            'code' => '75'
        ]);

        $response->assertSessionHasErrors(['code']);

        // Nom trop court
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'A',
            'code' => '99'
        ]);

        $response->assertSessionHasErrors(['name']);

        // Code non numérique
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Test Department',
            'code' => 'AB'
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    /**
     * Test que les erreurs de validation ne sont pas exposées aux users
     */
    public function test_user_gets_403_not_validation_errors()
    {
        // Même avec des données invalides, user doit recevoir 403
        $response = $this->actingAs($this->user)->post('/departments', [
            'name' => 'A', // Trop court
            'code' => 'XX' // Non numérique
        ]);

        $response->assertStatus(403);
        $response->assertDontSee('validation');
    }
}
