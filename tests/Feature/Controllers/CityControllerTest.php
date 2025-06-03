<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\City;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les rôles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Créer un utilisateur admin
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        // Créer un département de référence pour les tests
        $this->department = Department::factory()->create([
            'name' => 'Ain',
            'code' => '01'
        ]);
    }

    public function test_index_returns_cities_view()
    {
        // Créer quelques villes de test
        City::factory()->create([
            'name' => 'Bourg-en-Bresse',
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'name' => 'Oyonnax',
            'department_id' => $this->department->id
        ]);
        
        $response = $this->actingAs($this->admin)->get('/cities');
        
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
            'name' => 'Bourg-en-Bresse',
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'name' => 'Oyonnax',
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($this->admin)->get('/cities?search=Bourg');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('cities.data', 1)
                ->where('cities.data.0.name', 'Bourg-en-Bresse')
        );
    }

    public function test_index_filters_by_department()
    {
        $otherDepartment = Department::factory()->create(['name' => 'Aisne', 'code' => '02']);
        
        City::factory()->create([
            'name' => 'Bourg-en-Bresse',
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'name' => 'Laon',
            'department_id' => $otherDepartment->id
        ]);

        $response = $this->actingAs($this->admin)->get("/cities?department_id={$this->department->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('cities.data', 1)
                ->where('cities.data.0.name', 'Bourg-en-Bresse')
        );
    }

    public function test_index_filters_by_population()
    {
        City::factory()->create([
            'name' => 'Grande Ville',
            'population' => 100000,
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'name' => 'Petite Ville',
            'population' => 5000,
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($this->admin)->get('/cities?population_operator=gt&population_value=50000');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('cities.data', 1)
                ->where('cities.data.0.name', 'Grande Ville')
        );
    }

    public function test_index_sorts_cities()
    {
        City::factory()->create([
            'name' => 'Zebra City',
            'department_id' => $this->department->id
        ]);
        City::factory()->create([
            'name' => 'Alpha City',
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($this->admin)->get('/cities?sort=name&direction=asc');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->where('cities.data.0.name', 'Alpha City')
                ->where('cities.data.1.name', 'Zebra City')
        );
    }

    public function test_store_creates_new_city()
    {
        $cityData = [
            'name' => 'Test City',
            'postal_code' => '01000',
            'population' => 25000,
            'department_id' => $this->department->id
        ];

        $response = $this->actingAs($this->admin)->post('/cities', $cityData);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', $cityData);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)->post('/cities', []);

        $response->assertSessionHasErrors(['name', 'postal_code', 'department_id']);
    }

    public function test_store_validates_department_exists()
    {
        $response = $this->actingAs($this->admin)->post('/cities', [
            'name' => 'Test City',
            'postal_code' => '01000',
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

        $response = $this->actingAs($this->admin)->get("/cities/{$city->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $city->id,
            'name' => $city->name,
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name
            ]
        ]);
    }

    public function test_update_modifies_city()
    {
        $city = City::factory()->create([
            'department_id' => $this->department->id
        ]);
        $updateData = [
            'name' => 'Updated City',
            'postal_code' => '01999',
            'population' => 50000,
            'department_id' => $this->department->id
        ];

        $response = $this->actingAs($this->admin)->put("/cities/{$city->id}", $updateData);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', array_merge(['id' => $city->id], $updateData));
    }

    public function test_destroy_deletes_city()
    {
        $city = City::factory()->create([
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($this->admin)->delete("/cities/{$city->id}");

        $response->assertRedirect('/cities');
        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }
}