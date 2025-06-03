<?php

namespace Tests\Feature\Validation;

use Tests\TestCase;
use App\Models\Department;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class ValidationEdgeCasesTest extends TestCase
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

    public function test_department_code_leading_zeros_are_preserved()
    {
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Ain',
            'code' => '01'
        ]);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', [
            'name' => 'Ain',
            'code' => '01' // Doit conserver le 0 initial
        ]);
    }

    public function test_empty_strings_are_treated_as_missing_values()
    {
        $department = Department::factory()->create();

        // Test avec chaînes vides
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => '',
            'postal_code' => '',
            'population' => '',
            'department_id' => $department->id
        ]);

        $response->assertSessionHasErrors(['name', 'postal_code']);
    }

    public function test_whitespace_only_values_are_rejected()
    {
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => '   ', // Seulement des espaces
            'code' => '99'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_sql_injection_attempt_is_safely_handled()
    {
        $department = Department::factory()->create();
        
        // Tentative d'injection SQL
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => "Paris'; DROP TABLE cities; --",
            'postal_code' => '75000',
            'population' => 1000,
            'department_id' => $department->id
        ]);

        // La validation regex devrait rejeter ce nom
        $response->assertSessionHasErrors(['name']);
        
        // Vérifier que la table existe toujours
        $this->assertDatabaseCount('cities', 0);
    }

    public function test_xss_attempt_in_department_name_is_handled()
    {
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => '<script>alert("XSS")</script>',
            'code' => '99'
        ]);

        // La validation regex devrait rejeter ce nom
        $response->assertSessionHasErrors(['name']);
    }

    public function test_unicode_characters_in_city_names()
    {
        $department = Department::factory()->create();
        
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Châteauneuf-du-Pape',
            'postal_code' => '84230',
            'population' => 2000,
            'department_id' => $department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', ['name' => 'Châteauneuf-du-Pape']);
    }

    public function test_extremely_long_valid_name_at_limit()
    {
        // Test avec exactement 100 caractères (la limite)
        $longName = str_repeat('Saint-', 16) . 'Étie'; // Exactement 100 caractères
        $department = Department::factory()->create();
        
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => $longName,
            'postal_code' => '42000',
            'population' => 1000,
            'department_id' => $department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', ['name' => $longName]);
    }

    public function test_zero_population_is_valid()
    {
        $department = Department::factory()->create();
        
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Ghost Town',
            'postal_code' => '00000',
            'population' => 0, // Population zéro
            'department_id' => $department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'name' => 'Ghost Town',
            'population' => 0
        ]);
    }

    public function test_department_code_can_be_zero()
    {
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Special Department',
            'code' => '00'
        ]);

        // Le code '00' est valide selon nos règles actuelles
        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', [
            'name' => 'Special Department',
            'code' => '00'
        ]);
    }

    public function test_concurrent_creation_of_departments_with_same_code()
    {
        // Simuler une création concurrente
        Department::factory()->create(['code' => '99']);
        
        // Tenter de créer un autre avec le même code
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Duplicate',
            'code' => '99'
        ]);

        $response->assertSessionHasErrors(['code']);
        $this->assertCount(1, Department::where('code', '99')->get());
    }

    public function test_special_characters_in_search_are_escaped()
    {
        $department = Department::factory()->create();
        City::factory()->create([
            'name' => 'Saint-Étienne',
            'department_id' => $department->id
        ]);

        // Recherche avec caractères spéciaux
        $response = $this->actingAs($this->admin)->get('/cities?search=Saint%25'); // % encodé
        
        $response->assertStatus(200);
    }

    public function test_null_values_in_optional_fields()
    {
        $department = Department::factory()->create();
        
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '12345',
            'population' => null, // Explicitement null
            'department_id' => $department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'name' => 'Test City',
            'population' => null
        ]);
    }

    public function test_float_population_is_rejected()
    {
        $department = Department::factory()->create();
        
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '12345',
            'population' => '1000.5', // Float sous forme de string
            'department_id' => $department->id
        ]);

        // La validation integer devrait rejeter les floats
        $response->assertSessionHasErrors(['population']);
    }
}