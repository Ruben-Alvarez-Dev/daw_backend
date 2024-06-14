<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// User routes
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);

// Table routes
Route::get('/tables', [TableController::class, 'index']);
Route::post('/tables', [TableController::class, 'store']);
Route::get('/tables/{table}', [TableController::class, 'show']);
Route::put('/tables/{table}', [TableController::class, 'update']);
Route::delete('/tables/{table}', [TableController::class, 'destroy']);

// Reservation routes
Route::get('/reservations', [ReservationController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);
Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);