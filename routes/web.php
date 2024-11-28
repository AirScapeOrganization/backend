<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas de usuarios
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'edit']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);

Route::post('/login', [AuthController::class, 'login']);


// Rutas de listings
Route::get('/listings', [ListingsController::class, 'index']);
Route::get('/listings/{id}', [ListingsController::class, 'show']);
Route::post('/listings', [ListingsController::class, 'store']);
Route::put('/listings/{id}', [ListingsController::class, 'edit']);

// Ruta Reviews
Route::get('/reviews', [ReviewsController::class, 'index']);
Route::get('/reviews/{id}', [ReviewsController::class, 'show']);

//Autenticación
Route::middleware('auth.jwt')->group(function () {
  

});

