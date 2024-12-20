<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateJWT;
use App\Http\Middleware\ValidateTokenOwner;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/user', [UserController::class, 'store']);
Route::get('/listings', [ListingsController::class, 'index']);
Route::get('/listings/{id}', [ListingsController::class, 'show']);
Route::post('/login', [AuthController::class, 'login']);


// Private routes
Route::middleware([AuthenticateJWT::class])->group(function () {
    //Users
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/user/{id}', [UserController::class, 'edit']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // Listings
    Route::put('/listings/{id}', [ListingsController::class, 'edit']);

    //Reviews
    Route::get('/reviews', [ReviewsController::class, 'index']);
    Route::get('/reviews/{id}', [ReviewsController::class, 'show']);
    Route::post('/reviews', [ReviewsController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewsController::class, 'update']);

    //Notifications
    Route::get('/invoice/{id}', [InvoiceController::class, 'show']);

    //Bookings
    Route::get('/bookings', [BookingsController::class, 'show']);
    Route::post('/bookings', [BookingsController::class, 'store']);


    Route::middleware([ValidateTokenOwner::class])->group(function () {
        Route::post('/listings', [ListingsController::class, 'store']);
        Route::post('/photos', [PhotosController::class, 'store']);
    });
});
