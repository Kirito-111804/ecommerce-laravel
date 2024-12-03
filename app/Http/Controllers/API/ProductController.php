<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller

{
    // Fetch all products with optional search and category filter
    public function index(Request $request)
    {
        // Initialize a query on the Product model
        $query = Product::query();

        // Check if a search term is provided
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->where('description', 'like', '%' . $searchTerm . '%'); // Search by description
        }

        // Check if a category is provided
        if ($request->has('category') && $request->category !== '') {
            $category = $request->category;
            $query->where('category', $category); // Filter by category
        }

        // Get the filtered products
        $products = $query->get();

        return response()->json($products);
    }

    // Add a new product
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'barcode' => 'required|unique:products',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category' => 'required|string',
        ]);

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    // Update an existing product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'barcode' => 'required|unique:products,barcode,' . $id,
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category' => 'required|string',
        ]);

        $product->update($validatedData);

        return response()->json($product);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    // Show a single product (if needed)
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }
}