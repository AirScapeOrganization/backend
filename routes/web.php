<?php

use App\Http\Controllers\ListingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::apiResource('productos', UserController::class);
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'edit']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);



//listings

Route::get('/listings', [ListingsController::class,  'index']);
Route::post('/listings', [ListingsController::class,  'store']);
Route::put('/listings/{id}', [ListingsController::class,  'edit']);




use App\Http\Controllers\AuthController;



