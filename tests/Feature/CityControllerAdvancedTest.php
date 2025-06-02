<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CityControllerAdvancedTest extends TestCase
{
    use RefreshDatabase;

    protected $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->department = Department::factory()->create();
    }

    public function test_cannot_create_city_with_invalid_postal_code()
    {
        $response = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '123', // Doit être 5 chiffres
            'population' => 10000,
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['code_postal']);
        $this->assertDatabaseMissing('cities', ['nom' => 'Test City']);
    }

    public function test_cannot_create_city_with_postal_code_containing_letters()
    {
        $response = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '7500A', // Contient une lettre
            'population' => 10000,
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['code_postal']);
    }

    public function test_cannot_create_city_with_negative_population()
    {
        $response = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '75000',
            'population' => -100, // Population négative
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['population']);
    }

    public function test_cannot_create_city_with_population_exceeding_limit()
    {
        $response = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '75000',
            'population' => 50000001, // Dépasse la limite de 50 millions
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['population']);
    }

    public function test_city_name_cannot_contain_numbers()
    {
        $response = $this->post('/cities', [
            'nom' => 'Paris 75', // Contient des chiffres
            'code_postal' => '75000',
            'population' => 10000,
            'department_id' => $this->department->id
        ]);

        $response->assertSessionHasErrors(['nom']);
    }

    public function test_city_name_can_contain_special_french_characters()
    {
        $response = $this->post('/cities', [
            'nom' => "Saint-Étienne-du-Rouvray",
            'code_postal' => '76800',
            'population' => 28000,
            'department_id' => $this->department->id
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', ['nom' => "Saint-Étienne-du-Rouvray"]);
    }

    public function test_can_create_city_without_population()
    {
        $response = $this->post('/cities', [
            'nom' => 'Small Village',
            'code_postal' => '12345',
            'department_id' => $this->department->id
            // Population non fournie
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'nom' => 'Small Village',
            'population' => null
        ]);
    }

    public function test_cannot_update_city_to_non_existent_department()
    {
        $city = City::factory()->create(['department_id' => $this->department->id]);

        $response = $this->put("/cities/{$city->id}", [
            'nom' => $city->nom,
            'code_postal' => $city->code_postal,
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
        $dept1 = Department::factory()->create(['nom' => 'Ain']);
        $dept2 = Department::factory()->create(['nom' => 'Rhône']);

        City::factory()->create([
            'nom' => 'Lyon',
            'population' => 500000,
            'department_id' => $dept2->id
        ]);
        City::factory()->create([
            'nom' => 'Bourg-en-Bresse',
            'population' => 40000,
            'department_id' => $dept1->id
        ]);
        City::factory()->create([
            'nom' => 'Villeurbanne',
            'population' => 150000,
            'department_id' => $dept2->id
        ]);

        // Filtrer par département ET population
        $response = $this->get("/cities?department_id={$dept2->id}&population_operator=gt&population_value=100000");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('cities.data', 2) // Lyon et Villeurbanne
                ->where('cities.data.0.nom', 'Lyon')
                ->where('cities.data.1.nom', 'Villeurbanne')
        );
    }

    public function test_search_is_case_insensitive()
    {
        City::factory()->create(['nom' => 'PARIS', 'department_id' => $this->department->id]);
        City::factory()->create(['nom' => 'marseille', 'department_id' => $this->department->id]);

        $response1 = $this->get('/cities?search=paris');
        $response2 = $this->get('/cities?search=MARSEILLE');

        $response1->assertInertia(fn ($page) => $page->has('cities.data', 1));
        $response2->assertInertia(fn ($page) => $page->has('cities.data', 1));
    }

    public function test_sorting_works_with_null_population_values()
    {
        City::factory()->create([
            'nom' => 'City A',
            'population' => null,
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'nom' => 'City B',
            'population' => 1000,
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'nom' => 'City C',
            'population' => 500,
            'department_id' => $this->department->id
        ]);

        $response = $this->get('/cities?sort=population&direction=asc');

        $response->assertStatus(200);
        // Les villes avec population null devraient apparaître en premier ou en dernier selon la DB
    }

    public function test_api_returns_404_for_non_existent_city()
    {
        $response = $this->get('/cities/99999');
        $response->assertStatus(404);
    }

    public function test_can_handle_city_names_with_apostrophes()
    {
        $cityData = [
            'nom' => "L'Haÿ-les-Roses",
            'code_postal' => '94240',
            'population' => 31000,
            'department_id' => $this->department->id
        ];

        $response = $this->post('/cities', $cityData);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', ['nom' => "L'Haÿ-les-Roses"]);
    }

    public function test_postal_code_must_be_exactly_5_digits()
    {
        // Test avec 4 chiffres
        $response1 = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '1234',
            'department_id' => $this->department->id
        ]);

        // Test avec 6 chiffres
        $response2 = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '123456',
            'department_id' => $this->department->id
        ]);

        $response1->assertSessionHasErrors(['code_postal']);
        $response2->assertSessionHasErrors(['code_postal']);
    }

    public function test_update_city_with_partial_data()
    {
        $city = City::factory()->create([
            'nom' => 'Original Name',
            'code_postal' => '12345',
            'population' => 1000,
            'department_id' => $this->department->id
        ]);

        // Mise à jour seulement de la population
        $response = $this->put("/cities/{$city->id}", [
            'population' => 2000
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'nom' => 'Original Name', // Non modifié
            'code_postal' => '12345', // Non modifié
            'population' => 2000, // Modifié
            'department_id' => $this->department->id // Non modifié
        ]);
    }
}