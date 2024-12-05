<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::post('/login', [UserController::class, 'login']);  // Removed space between 'login' and 'login'
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
