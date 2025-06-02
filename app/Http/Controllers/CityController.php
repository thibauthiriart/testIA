<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Department;
use App\Http\Requests\IndexCityRequest;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexCityRequest $request = null)
    {
        // Version complète pour Inertia
        if (!$request) {
            $request = app(IndexCityRequest::class);
        }
        
        $query = City::with('department');

        // Récupérer les données validées
        $validated = $request->validated();

        // Sorting
        $sortField = $validated['sort'] ?? 'name';
        $sortDirection = $validated['direction'] ?? 'asc';

        // Détermine si on a besoin de join
        $needsJoin = $sortField === 'department' ||
                     isset($validated['department_search']);

        // Join avec departments si nécessaire pour le tri ou la recherche
        if ($needsJoin) {
            $query->join('departments', 'cities.department_id', '=', 'departments.id')
                  ->select('cities.*');
        }

        // Search cities filter
        if (isset($validated['search'])) {
            $query->where('cities.name', 'like', '%' . $validated['search'] . '%');
        }

        // Department search/filter
        if (isset($validated['department_search'])) {
            $query->where('departments.name', 'like', '%' . $validated['department_search'] . '%');
        }

        if (isset($validated['department_id'])) {
            $query->where('cities.department_id', $validated['department_id']);
        }

        // Population filter
        if (isset($validated['population_operator']) && isset($validated['population_value'])) {
            $operator = match($validated['population_operator']) {
                'gt' => '>',
                'lt' => '<',
                'eq' => '=',
                default => '='
            };
            $query->where('cities.population', $operator, $validated['population_value']);
        }

        // Apply sorting
        if ($sortField === 'department') {
            $query->orderBy('departments.name', $sortDirection);
        } else {
            $query->orderBy('cities.' . $sortField, $sortDirection);
        }

        // Pagination avec nombre d'items personnalisable
        $perPage = $validated['per_page'] ?? 10;
        $cities = $query->paginate($perPage)->withQueryString();
        $departments = Department::all();

        return inertia('Cities/Index', [
            'cities' => $cities,
            'departments' => $departments,
            'filters' => [
                'search' => $validated['search'] ?? '',
                'department_search' => $validated['department_search'] ?? '',
                'department_id' => $validated['department_id'] ?? '',
                'population_operator' => $validated['population_operator'] ?? '',
                'population_value' => $validated['population_value'] ?? '',
                'sort' => $sortField,
                'direction' => $sortDirection,
                'per_page' => $perPage
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        City::create($request->validated());
        return redirect()->route('cities.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $city = City::with('department')->findOrFail($id);
        return response()->json($city);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, string $id)
    {
        $city = City::findOrFail($id);
        $city->update($request->validated());
        return redirect()->route('cities.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return redirect()->route('cities.index');
    }
}
