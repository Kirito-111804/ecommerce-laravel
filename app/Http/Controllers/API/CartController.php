<?php

// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve the user's cart items with related products
        $cartItems = Cart::with('product')
                        ->where('user_id', Auth::id())  // Get the cart items for the authenticated user
                        ->get();

        return view('cart.index', compact('cartItems'));
    }

    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToCart(Request $request, $productId)
    {
        // Validate the input (quantity is required and must be a positive integer)
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Retrieve the product by its ID
        $product = Product::findOrFail($productId);

        // Check if the product already exists in the user's cart
        $existingCartItem = Cart::where('user_id', Auth::id())
                                ->where('product_id', $product->id)
                                ->first();

        if ($existingCartItem) {
            // If the product exists in the cart, update the quantity
            $existingCartItem->quantity += $request->quantity;
            $existingCartItem->save();
        } else {
            // If the product doesn't exist in the cart, create a new cart item
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,  // Assuming the price is retrieved from the product
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    /**
     * Update the quantity of an item in the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cartId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCart(Request $request, $cartId)
    {
        // Validate the input (quantity is required and must be a positive integer)
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the cart item by its ID
        $cartItem = Cart::findOrFail($cartId);

        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Update the quantity of the cart item
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    /**
     * Remove an item from the cart.
     *
     * @param  int  $cartId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromCart($cartId)
    {
        // Find the cart item by its ID
        $cartItem = Cart::findOrFail($cartId);

        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the cart item
        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Product removed from cart.');
    }

    /**
     * Clear the user's entire cart.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCart()
    {
        // Delete all cart items for the authenticated user
        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }
}
