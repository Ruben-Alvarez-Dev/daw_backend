<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;

// Rutas públicas de autenticación
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('complete-registration', 'completeRegistration');
});

// Rutas protegidas
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('profile', 'profile');
    });
    
    // Users - CRUD completo
    Route::apiResource('users', UserController::class);
    Route::post('users/simple', [UserController::class, 'createSimpleUser']);
    
    // Tables - CRUD completo
    Route::apiResource('tables', TableController::class);
    
    // Reservations - CRUD completo
    Route::get('my-reservations', [ReservationController::class, 'myReservations']);
    Route::apiResource('reservations', ReservationController::class);
});
