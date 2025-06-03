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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::put('/users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole']);
});

// Map API routes - public for now
Route::get('/map/cities', [MapController::class, 'getCities']);