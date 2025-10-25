<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlataformaController;
use App\Http\Controllers\Api\VideojuegoController;
use Illuminate\Support\Facades\Route;



// API V1 Routes
Route::prefix('v1')->group(function () {
    
    // Authentication Routes (Public)
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Routes (Require Authentication)
    Route::middleware('auth:api')->group(function () {
        
        // Auth Routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);

        // Videojuegos Routes
        Route::get('/videojuegos', [VideojuegoController::class, 'index']);
        Route::get('/videojuegos/recientes', [VideojuegoController::class, 'recientes']);
        Route::get('/videojuegos/{id}', [VideojuegoController::class, 'show']);
        Route::post('/videojuegos', [VideojuegoController::class, 'store']);
        Route::put('/videojuegos/{id}', [VideojuegoController::class, 'update']);
        Route::delete('/videojuegos/{id}', [VideojuegoController::class, 'destroy']);

        // Plataformas Routes
        Route::get('/plataformas', [PlataformaController::class, 'index']);
        Route::get('/plataformas/mas-popular', [PlataformaController::class, 'masPopular']);
        Route::get('/plataformas/{id}', [PlataformaController::class, 'show']);
        Route::post('/plataformas', [PlataformaController::class, 'store']);
        Route::put('/plataformas/{id}', [PlataformaController::class, 'update']);
        Route::delete('/plataformas/{id}', [PlataformaController::class, 'destroy']);
    });
});