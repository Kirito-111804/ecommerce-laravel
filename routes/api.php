<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController; // Ensure UserController is correctly imported
use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\CartController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group. Enjoy building your API!
*/

// Route for fetching the authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Product CRUD routes
Route::apiResource('products', ProductController::class);

// Register User Route
Route::post('/register', [RegistrationController::class, 'register']);

// Login Route
Route::post('/login', [UserController::class, 'login']);

// Logout Route
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'index']);

Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart', [CartController::class, 'addToCart']);
Route::put('/cart/{id}', [CartController::class, 'updateCart']);
Route::delete('/cart/{id}', [CartController::class, 'removeFromCart']);
Route::delete('/cart', [CartController::class, 'clearCart']);