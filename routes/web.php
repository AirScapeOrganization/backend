<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\ReviewsController;
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
    //Users
    Route::get('/user', [UserController::class, 'index']);
    
    Route::put('/user/{id}', [UserController::class, 'edit']);

    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    
    // Check username user
    Route::get('/check-username/{username}', [UserController::class, 'checkUsername']);

    // Check email user
    Route::get('/check-email/{email}', [UserController::class, 'checkEmail']);

    // Listings
    Route::get('/listings/{id}', [ListingsController::class, 'show']);
    Route::put('/listings/{id}', [ListingsController::class, 'edit']);
});

Route::post('/listings', [ListingsController::class, 'store']);


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

//  Rutas Invoices
//Route::get('/invoice', [InvoiceController::class, 'index']);
Route::get('/invoice/{id}', [InvoiceController::class, 'show']);
Route::post('invoice', [InvoiceController::class, 'store']);

