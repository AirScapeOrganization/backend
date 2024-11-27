<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas de usuarios
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'edit']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);

Route::post('/login', [AuthController::class, 'login']);

//Autenticación
Route::middleware('auth.jwt')->group(function () {
    Route::get('/listings', [ListingsController::class, 'index']);
    Route::post('/listings', [ListingsController::class, 'store']); // Ruta protegida
});

