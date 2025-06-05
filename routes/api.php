<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::put('/users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole']);
});

// Map API routes - protected with auth:api
Route::middleware('auth:api')->get('/map/cities', [MapController::class, 'getCities']);

// Properties API routes - protected with auth:api
Route::middleware('auth:api')->group(function () {
    Route::get('/properties', [\App\Http\Controllers\PropertyController::class, 'index']);
    Route::get('/properties/search', [\App\Http\Controllers\PropertyController::class, 'searchByLocation']);
    Route::get('/properties/{property}', [\App\Http\Controllers\PropertyController::class, 'show']);
});

// Cities API routes - admin only
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/cities', [\App\Http\Controllers\CityController::class, 'index']);
    Route::post('/cities', [\App\Http\Controllers\CityController::class, 'store']);
    Route::put('/cities/{city}', [\App\Http\Controllers\CityController::class, 'update']);
    Route::delete('/cities/{city}', [\App\Http\Controllers\CityController::class, 'destroy']);
});

// Departments API routes - admin only
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/departments', [\App\Http\Controllers\DepartmentController::class, 'index']);
    Route::post('/departments', [\App\Http\Controllers\DepartmentController::class, 'store']);
    Route::put('/departments/{department}', [\App\Http\Controllers\DepartmentController::class, 'update']);
    Route::delete('/departments/{department}', [\App\Http\Controllers\DepartmentController::class, 'destroy']);
});

// Users API routes - admin only
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy']);
});

// Dashboard API routes - authenticated users
Route::middleware('auth:api')->get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'getStats']);

// Account API routes - authenticated users
Route::middleware('auth:api')->group(function () {
    Route::put('/account', [\App\Http\Controllers\Auth\AuthController::class, 'updateAccount']);
});

// Scraper API routes - authenticated users (can be restricted to roles later)
Route::middleware('auth:api')->prefix('scrapers')->group(function () {
    Route::prefix('agences-en-limousin')->group(function () {
        Route::post('/scrape', [\App\Http\Controllers\Scrapers\AgencesEnLimousinScraperController::class, 'scrape']);
    });
});

// Scraper API routes - admin only
Route::middleware(['auth:api', 'role:admin'])->prefix('scrapers')->group(function () {
    // SeLoger scraper routes
    Route::prefix('seloger')->group(function () {
        Route::post('/listing', [\App\Http\Controllers\Scrapers\SeLogerScraperController::class, 'scrapeListingPage']);
        Route::post('/property', [\App\Http\Controllers\Scrapers\SeLogerScraperController::class, 'scrapePropertyDetails']);
        Route::post('/multiple-pages', [\App\Http\Controllers\Scrapers\SeLogerScraperController::class, 'scrapeMultiplePages']);
    });
});

// Temporary test routes without auth
Route::prefix('test-scrapers')->group(function () {
    Route::prefix('seloger')->group(function () {
        Route::post('/listing', [\App\Http\Controllers\Scrapers\SeLogerScraperController::class, 'scrapeListingPage']);
        Route::post('/property', [\App\Http\Controllers\Scrapers\SeLogerScraperController::class, 'scrapePropertyDetails']);
        Route::post('/multiple-pages', [\App\Http\Controllers\Scrapers\SeLogerScraperController::class, 'scrapeMultiplePages']);
    });
});