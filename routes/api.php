<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\MapLayoutController;

// Rutas públicas de autenticación
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login/email', 'loginWithEmail');
    Route::post('login/phone', 'loginWithPhone');
    Route::post('complete-registration', 'completeRegistration');
});

// Rutas protegidas
Route::middleware(['jwt.auth'])->group(function () {
    // Auth
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('profile', 'profile');
    });
    
    // Users - CRUD completo
    Route::apiResource('users', UserController::class);
    Route::post('users/simple', [UserController::class, 'createSimpleUser']);
    Route::get('users/search', [UserController::class, 'search']);
    
    // Tables - CRUD completo
    Route::apiResource('tables', TableController::class);
    
    // Reservations - CRUD completo
    Route::get('my-reservations', [ReservationController::class, 'myReservations']);
    Route::apiResource('reservations', ReservationController::class);
    Route::get('/reservations/date/{date}', [ReservationController::class, 'getByDate']);
    
    // Map Layouts
    Route::get('/map-layouts', [MapLayoutController::class, 'index']);
    Route::post('/map-layouts', [MapLayoutController::class, 'store']);
    Route::get('/map-layouts/default', [MapLayoutController::class, 'getDefault']);
    Route::post('/map-layouts/{id}/default', [MapLayoutController::class, 'setDefault']);
    Route::delete('/map-layouts/{id}', [MapLayoutController::class, 'destroy']);
    
    // Rutas de configuración
    Route::get('/config', [ConfigController::class, 'getConfig']);
    Route::put('/config', [ConfigController::class, 'updateConfig']);
});
