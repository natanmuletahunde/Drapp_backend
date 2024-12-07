<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [App\Http\Controllers\UsersController::class, 'login']);
Route::post('/register', [App\Http\Controllers\UsersController::class, 'register']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
