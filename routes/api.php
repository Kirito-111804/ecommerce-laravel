<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ProductController;

Route::get('/products', [ProductController::class, 'index']);    // GET all products
Route::post('/products', [ProductController::class, 'store']);   // POST (create) a new product
Route::get('/products/{id}', [ProductController::class, 'show']); // GET a specific product by ID
Route::put('/products/{id}', [ProductController::class, 'update']); // PUT (update) a product by ID
Route::delete('/products/{id}', [ProductController::class, 'destroy']); // DELETE a product by ID
