<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\CartItem; // Assuming you have a CartItem model
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'paymentMethod' => 'required|in:COD,Card',
            'cart' => 'required|array', // Array of cart items
        ]);

        // Start transaction
        DB::beginTransaction();
        try {
            // Create the order
            $order = Order::create([
                'name' => $request->name,
                'address' => $request->address,
                'payment_method' => $request->paymentMethod,
                'total_amount' => $request->totalAmount, // Assuming the total amount is sent from the frontend
            ]);

            // Save the cart items to an order_items table
            foreach ($request->cart as $item) {
                // Assuming you have a cart items table or can store order items in a related table
                $order->items()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully!',
                'order' => $order,
            ], 201);
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to place order. Please try again.',
            ], 500);
        }
    }
}
