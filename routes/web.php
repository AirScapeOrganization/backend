<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas de usuarios
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'edit']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);

// Ruta login
Route::post('/login', [AuthController::class, 'login']);


// Rutas de listings
Route::get('/listings', [ListingsController::class, 'index']);
Route::get('/listings/{id}', [ListingsController::class, 'show']);
Route::post('/listings', [ListingsController::class, 'store']);
Route::put('/listings/{id}', [ListingsController::class, 'edit']);

// Rutas Reviews
Route::get('/reviews', [ReviewsController::class, 'index']);
Route::get('/reviews/{id}', [ReviewsController::class, 'show']);
//Route::post('/reviews', [ReviewsController::class, 'store']);
Route::put('/reviews/{id}', [ReviewsController::class, 'update']);
Route::delete('/reviews/{id}', [ReviewsController::class, 'destroy']);

// Rutas bookings
Route::get('/bookings', [BookingsController::class, 'index']);
Route::get('/bookings/{id}', [BookingsController::class, 'show']);
Route::post('/bookings', [BookingsController::class, 'store']);

//Autenticación
Route::middleware('auth.jwt')->group(function () {

    
});

