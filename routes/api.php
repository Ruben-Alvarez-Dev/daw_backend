<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\TableController;
use App\Http\Controllers\Api\V1\ReservationController;
use Illuminate\Support\Facades\Route;

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

// Rutas públicas de autenticación
Route::group([
    'prefix' => 'v1/auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Rutas protegidas
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'v1'
], function () {
    // Rutas de autenticación que requieren token
    Route::group(['prefix' => 'auth'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Rutas para administradores
    Route::group(['middleware' => 'admin'], function () {
        // CRUD Usuarios
        Route::resource('users', UserController::class)->except(['create', 'edit']);
        Route::patch('users/{user}/status', [UserController::class, 'patch']);
        
        // CRUD Mesas
        Route::resource('tables', TableController::class)->except(['create', 'edit']);
        Route::patch('tables/{table}/status', [TableController::class, 'patch']);
        Route::get('tables/available', [TableController::class, 'available']);
        Route::get('tables/capacity/{min}', [TableController::class, 'findByCapacity']);
        
        // CRUD Reservas
        Route::resource('reservations', ReservationController::class)->except(['create', 'edit']);
        Route::patch('reservations/{reservation}/status', [ReservationController::class, 'patch']);
        Route::get('reservations/date/{date}', [ReservationController::class, 'findByDate']);
        Route::get('reservations/user/{user}', [ReservationController::class, 'findByUser']);
        Route::get('reservations/status/{status}', [ReservationController::class, 'findByStatus']);
    });

    // Rutas para customers
    Route::group(['middleware' => 'customer'], function () {
        // Perfil de usuario (solo el propio)
        Route::get('profile', [UserController::class, 'profile']);
        Route::put('profile', [UserController::class, 'updateProfile']);
        Route::patch('profile', [UserController::class, 'patchProfile']);

        // Reservas (limitadas)
        Route::get('my-reservations', [ReservationController::class, 'myReservations']);
        Route::get('my-reservations/{reservation}', [ReservationController::class, 'showMyReservation']);
        Route::post('reservations', [ReservationController::class, 'store']);
        
        // Consultas públicas
        Route::get('tables/available', [TableController::class, 'available']);
    });
});
