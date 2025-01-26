<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\MapLayoutController;
use App\Http\Controllers\MapTemplateController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftHistoryController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\TemplateController;

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
    
    // Tables
    Route::middleware('auth:api')->group(function () {
        Route::get('/tables', [TableController::class, 'index']);
        Route::post('/tables', [TableController::class, 'store']);
        Route::get('/tables/{table}', [TableController::class, 'show']);
        Route::put('/tables/{table}', [TableController::class, 'update']);
        Route::delete('/tables/{table}', [TableController::class, 'destroy']);
        Route::get('/templates/{templateId}/tables/{date?}', [TableController::class, 'getActiveByTemplate']);
    });
    
    // Reservations - CRUD completo
    Route::get('my-reservations', [ReservationController::class, 'myReservations']);
    Route::apiResource('reservations', ReservationController::class);
    Route::get('/reservations/date/{date}', [ReservationController::class, 'getByDate']);
    Route::get('/bookings', [ReservationController::class, 'getByDateAndShift']);
    Route::post('/bookings/{id}/assign-table', [ReservationController::class, 'assignTable']);
    
    // Map Layouts
    Route::get('/map-layouts', [MapLayoutController::class, 'index']);
    Route::post('/map-layouts', [MapLayoutController::class, 'store']);
    Route::get('/map-layouts/default', [MapLayoutController::class, 'getDefault']);
    Route::post('/map-layouts/{id}/default', [MapLayoutController::class, 'setDefault']);
    Route::delete('/map-layouts/{id}', [MapLayoutController::class, 'destroy']);
    
    // Map Templates
    Route::middleware('auth:api')->group(function () {
        Route::get('/templates', [MapTemplateController::class, 'index']);
        Route::post('/templates', [MapTemplateController::class, 'store']);
        Route::get('/templates/{mapTemplate}', [MapTemplateController::class, 'show']);
        Route::put('/templates/{mapTemplate}', [MapTemplateController::class, 'update']);
        Route::delete('/templates/{mapTemplate}', [MapTemplateController::class, 'destroy']);
        Route::get('/templates/default/{zone}', [MapTemplateController::class, 'getDefaultTemplate']);
    });
    
    // Mapas
    Route::get('/map', [MapController::class, 'index']);
    Route::post('/map', [MapController::class, 'store']);
    Route::put('/map/{map}', [MapController::class, 'update']);
    
    // Templates
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::post('/templates', [TemplateController::class, 'store']);
    Route::get('/templates/{template}', [TemplateController::class, 'show']);
    Route::put('/templates/{template}', [TemplateController::class, 'update']);
    Route::delete('/templates/{template}', [TemplateController::class, 'destroy']);
    Route::get('/templates/shift', [TemplateController::class, 'getForShift']);
    
    // Shifts
    Route::middleware('auth:api')->group(function () {
        Route::get('/shifts', [ShiftController::class, 'index']);
        Route::post('/shifts', [ShiftController::class, 'store']);
        Route::get('/shifts/{shift}', [ShiftController::class, 'show']);
        Route::put('/shifts/{shift}', [ShiftController::class, 'update']);
        Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy']);
        Route::get('/shifts/date/{date}', [ShiftController::class, 'getByDate']);
        
        // Shift Distributions
        Route::post('/shifts/{shift}/distributions', [ShiftController::class, 'addDistribution']);
        Route::put('/shifts/{shift}/distributions/{distribution}', [ShiftController::class, 'updateDistribution']);
    });
    
    // Shift History
    Route::middleware('auth:api')->group(function () {
        Route::get('/shifts/{shift}/history', [ShiftHistoryController::class, 'index']);
        Route::post('/shifts/{shift}/history', [ShiftHistoryController::class, 'store']);
        Route::get('/shifts/{shift}/history/{history}', [ShiftHistoryController::class, 'show']);
        Route::put('/shifts/{shift}/history/{history}', [ShiftHistoryController::class, 'update']);
        Route::delete('/shifts/{shift}/history/{history}', [ShiftHistoryController::class, 'destroy']);
        
        // Consultas especiales
        Route::get('/tables/{table}/history/{date}', [ShiftHistoryController::class, 'getTableHistory']);
        Route::get('/reservations/{reservation}/history', [ShiftHistoryController::class, 'getReservationHistory']);
    });
    
    // Rutas de configuración
    Route::get('/config', [ConfigController::class, 'getConfig']);
    Route::put('/config', [ConfigController::class, 'updateConfig']);
});
