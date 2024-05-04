<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\userController;
use App\Http\Controllers\tableController;
use App\Http\Controllers\reservationController;


Route::get('/tables', function () {return 'Tables';});
Route::get('/tables/{id}', function () {return 'Tables';});
Route::post('/tables', function () {return 'Tables';});
Route::put('/tables/{id}', function () {return 'Tables';});
Route::delete('/tables/{id}', function () {return 'Tables';}); 

Route::get('/users', function () {return 'Users';});
Route::get('/users/{id}', function () {return 'Users';});
Route::post('/users', function () {return 'Users';});
Route::put('/users/{id}', function () {return 'Users';});
Route::delete('/users/{id}', function () {return 'Users';});

Route::get('/reservations', [ReservationController::class, 'index']);
Route::get('/reservations/{id}', function () {return 'Orders';});
Route::post('/reservations', function () {return 'Orders';});
Route::put('/reservations/{id}', function () {return 'Orders';});
Route::delete('/reservations/{id}', function () {return 'Orders';});
