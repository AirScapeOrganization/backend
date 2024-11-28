<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Routes
// Users

Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);

// Listings

Route::get('/listings', [ListingsController::class, 'index']);

// Authentication

Route::post('/login', [AuthController::class, 'login']);

// Private routes

Route::middleware('auth.jwt')->group(function () {
    // Users

    Route::put('/user/{id}', [UserController::class, 'edit']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // Listings
    Route::post('/listings', [ListingsController::class, 'store']);
});

