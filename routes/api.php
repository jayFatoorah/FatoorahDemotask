<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login/{guard}', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});


Route::middleware(['auth:sanctum', 'auth.admin'])->group(function () {
    // Admin-only Routes
    Route::apiResource('users', UserController::class); 
    Route::get('users/{id}', [UserController::class, 'show']); 
});

Route::middleware(['auth:sanctum', 'auth.user'])->group(function () {
    // User-only Routes
    Route::get('profile', [UserController::class, 'showProfile']); 
    Route::put('profile', [UserController::class, 'updateProfile']);
});
