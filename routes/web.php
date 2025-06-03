<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    
    // Routes accessibles aux admins uniquement
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('departments', DepartmentController::class);
        Route::resource('cities', CityController::class);
        
        // Gestion des utilisateurs
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });
    
    Route::get('/account', [AuthController::class, 'showAccount'])->name('account');
    Route::put('/account', [AuthController::class, 'updateAccount']);
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
