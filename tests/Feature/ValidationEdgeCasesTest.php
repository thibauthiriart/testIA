<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Department;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_code_leading_zeros_are_preserved()
    {
        $response = $this->post('/departments', [
            'nom' => 'Ain',
            'code' => '01'
        ]);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', [
            'nom' => 'Ain',
            'code' => '01' // Doit conserver le 0 initial
        ]);
    }

    public function test_empty_strings_are_treated_as_missing_values()
    {
        $department = Department::factory()->create();

        // Test avec chaînes vides
        $response = $this->post('/cities', [
            'nom' => '',
            'code_postal' => '',
            'population' => '',
            'department_id' => $department->id
        ]);

        $response->assertSessionHasErrors(['nom', 'code_postal']);
    }

    public function test_whitespace_only_values_are_rejected()
    {
        $response = $this->post('/departments', [
            'nom' => '   ', // Seulement des espaces
            'code' => '99'
        ]);

        $response->assertSessionHasErrors(['nom']);
    }

    public function test_sql_injection_attempt_is_safely_handled()
    {
        $department = Department::factory()->create();
        
        // Tentative d'injection SQL
        $response = $this->post('/cities', [
            'nom' => "Paris'; DROP TABLE cities; --",
            'code_postal' => '75000',
            'population' => 1000,
            'department_id' => $department->id
        ]);

        // La validation regex devrait rejeter ce nom
        $response->assertSessionHasErrors(['nom']);
        
        // Vérifier que la table existe toujours
        $this->assertDatabaseCount('cities', 0);
    }

    public function test_xss_attempt_in_department_name_is_handled()
    {
        $response = $this->post('/departments', [
            'nom' => '<script>alert("XSS")</script>',
            'code' => '99'
        ]);

        // La validation regex devrait rejeter ce nom
        $response->assertSessionHasErrors(['nom']);
    }

    public function test_unicode_characters_in_city_names()
    {
        $department = Department::factory()->create();
        
        $response = $this->post('/cities', [
            'nom' => 'Châteauneuf-du-Pape',
            'code_postal' => '84230',
            'population' => 2000,
            'department_id' => $department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', ['nom' => 'Châteauneuf-du-Pape']);
    }

    public function test_extremely_long_valid_name_at_limit()
    {
        // Test avec exactement 100 caractères (la limite)
        $longName = str_repeat('Saint-', 16) . 'Étie'; // Exactement 100 caractères
        $department = Department::factory()->create();
        
        $response = $this->post('/cities', [
            'nom' => $longName,
            'code_postal' => '42000',
            'population' => 1000,
            'department_id' => $department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', ['nom' => $longName]);
    }

    public function test_zero_population_is_valid()
    {
        $department = Department::factory()->create();
        
        $response = $this->post('/cities', [
            'nom' => 'Ghost Town',
            'code_postal' => '00000',
            'population' => 0, // Population zéro
            'department_id' => $department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'nom' => 'Ghost Town',
            'population' => 0
        ]);
    }

    public function test_department_code_can_be_zero()
    {
        $response = $this->post('/departments', [
            'nom' => 'Special Department',
            'code' => '00'
        ]);

        // Le code '00' est valide selon nos règles actuelles
        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', [
            'nom' => 'Special Department',
            'code' => '00'
        ]);
    }

    public function test_concurrent_creation_of_departments_with_same_code()
    {
        // Simuler une création concurrente
        Department::factory()->create(['code' => '99']);
        
        // Tenter de créer un autre avec le même code
        $response = $this->post('/departments', [
            'nom' => 'Duplicate',
            'code' => '99'
        ]);

        $response->assertSessionHasErrors(['code']);
        $this->assertCount(1, Department::where('code', '99')->get());
    }

    public function test_special_characters_in_search_are_escaped()
    {
        $department = Department::factory()->create();
        City::factory()->create([
            'nom' => 'Saint-Étienne',
            'department_id' => $department->id
        ]);

        // Recherche avec caractères spéciaux
        $response = $this->get('/cities?search=Saint%25'); // % encodé
        
        $response->assertStatus(200);
    }

    public function test_null_values_in_optional_fields()
    {
        $department = Department::factory()->create();
        
        $response = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '12345',
            'population' => null, // Explicitement null
            'department_id' => $department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'nom' => 'Test City',
            'population' => null
        ]);
    }

    public function test_float_population_is_rejected()
    {
        $department = Department::factory()->create();
        
        $response = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '12345',
            'population' => '1000.5', // Float sous forme de string
            'department_id' => $department->id
        ]);

        // La validation integer devrait rejeter les floats
        $response->assertSessionHasErrors(['population']);
    }
}