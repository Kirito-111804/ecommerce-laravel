<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\CheckoutController;

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

// Authentication routes
Route::post('/register', [RegistrationController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/login', function () {
    return response()->json([
        'message' => 'Please Login.',
    ], 401);
})-> name('login');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// User management routes
Route::get('/users', [UserController::class, 'index'])->middleware('auth:sanctum');

// Checkout route
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->middleware('auth:sanctum');

// Cart routes
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']); // View cart items
    Route::post('/', [CartController::class, 'addToCart']); // Add item to cart
    Route::put('/{id}', [CartController::class, 'updateCart']); // Update item in cart
    Route::delete('/{id}', [CartController::class, 'removeFromCart']); // Remove item from cart
    Route::delete('/', [CartController::class, 'clearCart']); // Clear all items in cart
});