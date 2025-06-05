<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Department;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'departments_count' => Department::count(),
            'cities_count' => City::count(),
            'total_population' => City::sum('population'),
        ];
        
        return Inertia::render('Dashboard', ['stats' => $stats]);
    }

    public function getStats()
    {
        return response()->json([
            'departments_count' => Department::count(),
            'cities_count' => City::count(),
            'total_population' => City::sum('population'),
        ]);
    }
}