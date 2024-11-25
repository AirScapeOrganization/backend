<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::apiResource('productos', UserController::class);
Route::get('/', [UserController::class, 'index']);
Route::post('/post', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'edit']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);


use App\Http\Controllers\AuthController;



