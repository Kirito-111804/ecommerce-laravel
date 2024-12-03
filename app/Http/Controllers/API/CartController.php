<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; // Import the base Controller class

class CartController extends Controller
{
    public function index()
    {
        $userId = 1; // Replace with `Auth::id()` if authentication is enabled
        $cartItems = Cart::with('product')
            ->where('user_id', $userId)
            ->get();

        return response()->json($cartItems);
    }

    public function addToCart(Request $request)
    {
        $userId = 1; // Replace with `Auth::id()` if authentication is enabled

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient stock for this product.'], 400);
        }

        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($newQuantity > $product->quantity) {
                return response()->json(['message' => 'Insufficient stock for this product.'], 400);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->price = $product->price;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully.'], 201);
    }

    public function updateCart(Request $request, $id)
    {
        $userId = 1; // Replace with `Auth::id()` if authentication is enabled

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::findOrFail($id);

        if ($cartItem->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $product = $cartItem->product;

        if ($request->quantity > $product->quantity) {
            return response()->json(['message' => 'Insufficient stock for this product.'], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['message' => 'Cart updated successfully.']);
    }

    public function removeFromCart($id)
    {
        $userId = 1; // Replace with `Auth::id()` if authentication is enabled

        $cartItem = Cart::findOrFail($id);

        if ($cartItem->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart successfully.']);
    }

    public function clearCart()
    {
        $userId = 1; // Replace with `Auth::id()` if authentication is enabled

        Cart::where('user_id', $userId)->delete();

        return response()->json(['message' => 'Cart cleared successfully.']);
    }
}
