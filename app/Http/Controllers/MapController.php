<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{
    /**
     * Display the map page with all cities
     */
    public function index(): Response
    {
        return Inertia::render('Map/Index');
    }

    /**
     * Get all cities with coordinates for the map
     */
    public function getCities(): JsonResponse
    {
        $cities = City::with('department')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select([
                'id',
                'name', 
                'postal_code',
                'population',
                'latitude',
                'longitude',
                'department_id'
            ])
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'postal_code' => $city->postal_code,
                    'population' => $city->population,
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'department' => [
                        'name' => $city->department->name,
                        'code' => $city->department->code,
                    ]
                ];
            });

        return response()->json($cities);
    }
}
