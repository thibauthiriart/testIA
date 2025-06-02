<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Models\City;
use App\Models\Department;

// Routes publiques
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $stats = [
            'departments_count' => Department::count(),
            'cities_count' => City::count(),
            'total_population' => City::sum('population'),
        ];
        
        return inertia('Dashboard', ['stats' => $stats]);
    })->name('dashboard');
    
    Route::resource('departments', DepartmentController::class);
    Route::resource('cities', CityController::class);
    
    Route::get('/account', [AuthController::class, 'showAccount'])->name('account');
    Route::put('/account', [AuthController::class, 'updateAccount']);
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
