<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::get('/reservations', function () {return 'Orders';});
Route::get('/reservations/{id}', function () {return 'Orders';});
Route::post('/reservations', function () {return 'Orders';});
Route::put('/reservations/{id}', function () {return 'Orders';});
Route::delete('/reservations/{id}', function () {return 'Orders';});
