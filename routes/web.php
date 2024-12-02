<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateJWT;
use Illuminate\Support\Facades\Route;

// Public Routes
// Users
Route::post('/user', [UserController::class, 'store']);

// Listings
Route::get('/listings', [ListingsController::class, 'index']);

// Authentication
Route::post('/login', [AuthController::class, 'login']);

// Private routes
Route::middleware([AuthenticateJWT::class])->group(function () {
    // Show all users
    Route::get('/user', [UserController::class, 'index']);
    
    Route::put('/user/{id}', [UserController::class, 'edit']);

    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    
    // Check username user
    Route::get('/check-username/{username}', [UserController::class, 'checkUsername']);
    // Check email user

    Route::get('/check-email/{email}', [UserController::class, 'checkEmail']);

    // Listings
    Route::post('/listings', [ListingsController::class, 'store']);
});


