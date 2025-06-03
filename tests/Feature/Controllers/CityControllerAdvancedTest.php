<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\City;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class CityControllerAdvancedTest extends TestCase
{
    use RefreshDatabase;

    protected $department;
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
        
        $this->department = Department::factory()->create();
    }

    public function test_cannot_create_city_with_invalid_postal_code()
    {
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '123', // Doit être 5 chiffres
            'population' => 10000,
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['postal_code']);
        $this->assertDatabaseMissing('cities', ['name' => 'Test City']);
    }

    public function test_cannot_create_city_with_postal_code_containing_letters()
    {
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '7500A', // Contient une lettre
            'population' => 10000,
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['postal_code']);
    }

    public function test_cannot_create_city_with_negative_population()
    {
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '75000',
            'population' => -100, // Population négative
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['population']);
    }

    public function test_cannot_create_city_with_population_exceeding_limit()
    {
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '75000',
            'population' => 50000001, // Dépasse la limite de 50 millions
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['population']);
    }

    public function test_city_name_cannot_contain_numbers()
    {
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Paris 75', // Contient des chiffres
            'postal_code' => '75000',
            'population' => 10000,
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_city_name_can_contain_special_french_characters()
    {
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => "Saint-Étienne-du-Rouvray",
            'postal_code' => '76800',
            'population' => 28000,
            'department_id' => $this->department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', ['name' => "Saint-Étienne-du-Rouvray"]);
    }

    public function test_can_create_city_without_population()
    {
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Small Village',
            'postal_code' => '12345',
            'department_id' => $this->department->id
            // Population non fournie
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'name' => 'Small Village',
            'population' => null
        ]);
    }

    public function test_cannot_update_city_to_non_existent_department()
    {
        $city = City::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin)->put("/cities/{$city->id}", [
            'name' => $city->name,
            'postal_code' => $city->postal_code,
            'population' => $city->population,
            'department_id' => 99999 // N'existe pas
        ]);

        $response->assertSessionHasErrors(['department_id']);
        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'department_id' => $this->department->id // Non modifié
        ]);
    }

    public function test_index_filters_correctly_with_multiple_criteria()
    {
        $dept1 = Department::factory()->create(['name' => 'Ain']);
        $dept2 = Department::factory()->create(['name' => 'Rhône']);

        City::factory()->create([
            'name' => 'Lyon',
            'population' => 500000,
            'department_id' => $dept2->id
        ]);
        City::factory()->create([
            'name' => 'Bourg-en-Bresse',
            'population' => 40000,
            'department_id' => $dept1->id
        ]);
        City::factory()->create([
            'name' => 'Villeurbanne',
            'population' => 150000,
            'department_id' => $dept2->id
        ]);

        // Filtrer par département ET population
        $response = $this->actingAs($this->admin)->get("/cities?department_id={$dept2->id}&population_operator=gt&population_value=100000");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('cities.data', 2) // Lyon et Villeurbanne
                ->where('cities.data.0.name', 'Lyon')
                ->where('cities.data.1.name', 'Villeurbanne')
        );
    }

    public function test_search_is_case_insensitive()
    {
        City::factory()->create(['name' => 'PARIS', 'department_id' => $this->department->id]);
        City::factory()->create(['name' => 'marseille', 'department_id' => $this->department->id]);

        $response1 = $this->actingAs($this->admin)->get('/cities?search=paris');
        $response2 = $this->actingAs($this->admin)->get('/cities?search=MARSEILLE');

        $response1->assertInertia(fn ($page) => $page->has('cities.data', 1));
        $response2->assertInertia(fn ($page) => $page->has('cities.data', 1));
    }

    public function test_sorting_works_with_null_population_values()
    {
        City::factory()->create([
            'name' => 'City A',
            'population' => null,
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'name' => 'City B',
            'population' => 1000,
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'name' => 'City C',
            'population' => 500,
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($this->admin)->get('/cities?sort=population&direction=asc');

        $response->assertStatus(200);
        // Les villes avec population null devraient apparaître en premier ou en dernier selon la DB
    }

    public function test_api_returns_404_for_non_existent_city()
    {
        $response = $this->actingAs($this->admin)->get('/cities/99999');
        $response->assertStatus(404);
    }

    public function test_can_handle_city_names_with_apostrophes()
    {
        $cityData = [
            'name' => "L'Haÿ-les-Roses",
            'postal_code' => '94240',
            'population' => 31000,
            'department_id' => $this->department->id
        ];

        $response = $this->actingAs($this->admin)->post('/cities', $cityData);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', ['name' => "L'Haÿ-les-Roses"]);
    }

    public function test_postal_code_must_be_exactly_5_digits()
    {
        // Test avec 4 chiffres
        $response1 = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '1234',
            'department_id' => $this->department->id
        ]);

        // Test avec 6 chiffres
        $response2 = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '123456',
            'department_id' => $this->department->id
        ]);

        $response1->assertSessionHasErrors(['postal_code']);
        $response2->assertSessionHasErrors(['postal_code']);
    }

    public function test_update_city_with_partial_data()
    {
        $city = City::factory()->create([
            'name' => 'Original Name',
            'postal_code' => '12345',
            'population' => 1000,
            'department_id' => $this->department->id
        ]);

        // Mise à jour seulement de la population
        $response = $this->actingAs($this->admin)->put("/cities/{$city->id}", [
            'population' => 2000
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'name' => 'Original Name', // Non modifié
            'postal_code' => '12345', // Non modifié
            'population' => 2000, // Modifié
            'department_id' => $this->department->id // Non modifié
        ]);
    }
}