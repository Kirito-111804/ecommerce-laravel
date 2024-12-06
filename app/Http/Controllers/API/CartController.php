<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * Display the user's cart items.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        

        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems,
        ]);
    }

    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Optional: Check product stock
        // if ($product->stock < $request->quantity) {
        //     return response()->json(['success' => false, 'message' => 'Insufficient stock.'], 400);
        // }

        $cartItem = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        $cartItem->quantity += $request->quantity;
        $cartItem->price = $product->price;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart.',
            'data' => $cartItem,
        ]);
    }

    /**
     * Update the quantity of an item in the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cartId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCart(Request $request, $cartId)
    {
        // if (!Auth::check()) {
        //     return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        // }

        $request->validate(['quantity' => 'required|integer|min:1']);

        $cartItem = Cart::findOrFail($cartId);

        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Optional: Check product stock
        // if ($cartItem->product->stock < $request->quantity) {
        //     return response()->json(['success' => false, 'message' => 'Insufficient stock available.'], 400);
        // }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated.',
            'data' => $cartItem,
        ]);
    }

    /**
     * Remove an item from the cart.
     *
     * @param  int  $cartId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromCart($cartId)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $cartItem = Cart::findOrFail($cartId);

        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
        ]);
    }

    /**
     * Clear the user's entire cart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCart()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        Cart::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
        ]);
    }
}
