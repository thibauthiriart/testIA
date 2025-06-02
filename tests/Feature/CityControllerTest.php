<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un département de référence pour les tests
        $this->department = Department::factory()->create([
            'nom' => 'Ain',
            'code' => '01'
        ]);
    }

    public function test_index_returns_cities_view()
    {
        // Créer quelques villes de test
        City::factory()->create([
            'nom' => 'Bourg-en-Bresse',
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'nom' => 'Oyonnax',
            'department_id' => $this->department->id
        ]);
        
        $response = $this->get('/cities');
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Cities/Index')
                ->has('cities.data', 2)
                ->has('departments')
        );
    }

    public function test_index_filters_by_search()
    {
        City::factory()->create([
            'nom' => 'Bourg-en-Bresse',
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'nom' => 'Oyonnax',
            'department_id' => $this->department->id
        ]);

        $response = $this->get('/cities?search=Bourg');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('cities.data', 1)
                ->where('cities.data.0.nom', 'Bourg-en-Bresse')
        );
    }

    public function test_index_filters_by_department()
    {
        $otherDepartment = Department::factory()->create(['nom' => 'Aisne', 'code' => '02']);
        
        City::factory()->create([
            'nom' => 'Bourg-en-Bresse',
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'nom' => 'Laon',
            'department_id' => $otherDepartment->id
        ]);

        $response = $this->get("/cities?department_id={$this->department->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('cities.data', 1)
                ->where('cities.data.0.nom', 'Bourg-en-Bresse')
        );
    }

    public function test_index_filters_by_population()
    {
        City::factory()->create([
            'nom' => 'Grande Ville',
            'population' => 100000,
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'nom' => 'Petite Ville',
            'population' => 5000,
            'department_id' => $this->department->id
        ]);

        $response = $this->get('/cities?population_operator=gt&population_value=50000');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('cities.data', 1)
                ->where('cities.data.0.nom', 'Grande Ville')
        );
    }

    public function test_index_sorts_cities()
    {
        City::factory()->create([
            'nom' => 'Zebra City',
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'nom' => 'Alpha City',
            'department_id' => $this->department->id
        ]);

        $response = $this->get('/cities?sort=nom&direction=asc');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->where('cities.data.0.nom', 'Alpha City')
                ->where('cities.data.1.nom', 'Zebra City')
        );
    }

    public function test_store_creates_new_city()
    {
        $cityData = [
            'nom' => 'Test City',
            'code_postal' => '01000',
            'population' => 25000,
            'department_id' => $this->department->id
        ];

        $response = $this->post('/cities', $cityData);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', $cityData);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post('/cities', []);

        $response->assertSessionHasErrors(['nom', 'code_postal', 'department_id']);
    }

    public function test_store_validates_department_exists()
    {
        $response = $this->post('/cities', [
            'nom' => 'Test City',
            'code_postal' => '01000',
            'population' => 25000,
            'department_id' => 999
        ]);

        $response->assertSessionHasErrors(['department_id']);
    }

    public function test_show_returns_city_with_department()
    {
        $city = City::factory()->create([
            'department_id' => $this->department->id
        ]);

        $response = $this->get("/cities/{$city->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $city->id,
            'nom' => $city->nom,
            'department' => [
                'id' => $this->department->id,
                'nom' => $this->department->nom
            ]
        ]);
    }

    public function test_update_modifies_city()
    {
        $city = City::factory()->create([
            'department_id' => $this->department->id
        ]);
        $updateData = [
            'nom' => 'Updated City',
            'code_postal' => '01999',
            'population' => 50000,
            'department_id' => $this->department->id
        ];

        $response = $this->put("/cities/{$city->id}", $updateData);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', array_merge(['id' => $city->id], $updateData));
    }

    public function test_destroy_deletes_city()
    {
        $city = City::factory()->create([
            'department_id' => $this->department->id
        ]);

        $response = $this->delete("/cities/{$city->id}");

        $response->assertRedirect('/cities');
        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }
}