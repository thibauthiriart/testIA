<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\City;
use App\Services\CityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query()->with('cityModel');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('cityModel', function ($cityQuery) use ($search) {
                        $cityQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('postal_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->filled('min_surface')) {
            $query->where('surface', '>=', $request->input('min_surface'));
        }

        if ($request->filled('max_surface')) {
            $query->where('surface', '<=', $request->input('max_surface'));
        }

        if ($request->filled('rooms')) {
            $query->where('rooms', $request->input('rooms'));
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->input('property_type'));
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->input('transaction_type'));
        }

        $sortField = $request->input('sort', 'scraped_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $properties = $query->paginate(20)->withQueryString();

        // If this is an API request, return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($properties);
        }

        // Récupérer toutes les villes depuis la table cities
        $cities = City::select('id', 'name', 'postal_code')
            ->orderBy('name')
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name . ' (' . $city->postal_code . ')'
                ];
            });

        return Inertia::render('Properties/Index', [
            'properties' => $properties,
            'cities' => $cities,
            'filters' => $request->only([
                'search', 'city_id', 'min_price', 'max_price',
                'min_surface', 'max_surface', 'rooms',
                'property_type', 'transaction_type'
            ]),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    public function show(Property $property)
    {
        return Inertia::render('Properties/Show', [
            'property' => $property,
        ]);
    }
    
    public function searchByLocation(Request $request, CityService $cityService)
    {
        $validated = $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city' => 'nullable|string',
            'radius' => 'integer|min:1|max:100'
        ]);
        
        $radius = $validated['radius'] ?? 10; // Default 10km
        
        // If coordinates are provided, search by distance from coordinates
        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            
            // Find cities in radius
            $citiesInRadius = $cityService->findCitiesInRadius($latitude, $longitude, $radius);
            $cityIds = $citiesInRadius->pluck('id')->toArray();
            
            $properties = Property::whereIn('city_id', $cityIds)
                ->where('is_active', true)
                ->with('cityModel')
                ->limit(50)
                ->get();
                
            $searchCenter = ['latitude' => $latitude, 'longitude' => $longitude];
        }
        // Search by city name
        else if (!empty($validated['city'])) {
            // Find the city
            $city = City::whereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $validated['city'] . '%'])->first();
            
            if ($city) {
                // Get properties in radius around this city
                $properties = $cityService->getPropertiesInRadius($city, $radius);
                $searchCenter = ['latitude' => $city->latitude, 'longitude' => $city->longitude];
            } else {
                // No city found, return empty collection
                $properties = collect();
                $searchCenter = ['latitude' => null, 'longitude' => null];
            }
        } else {
            $properties = collect();
            $searchCenter = ['latitude' => null, 'longitude' => null];
        }
        
        return response()->json([
            'properties' => $properties,
            'search_center' => $searchCenter,
            'radius' => $radius,
            'count' => $properties->count()
        ]);
    }
}
