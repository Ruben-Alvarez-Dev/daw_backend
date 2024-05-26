<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

// Rutas para el controlador UserController
Route::apiResource('users', UserController::class);

// Rutas para el controlador TableController
Route::apiResource('tables', TableController::class);

// Rutas para el controlador ReservationController
Route::apiResource('reservations', ReservationController::class);