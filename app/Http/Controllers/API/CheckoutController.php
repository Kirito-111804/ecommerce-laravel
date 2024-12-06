<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem; // For saving order items
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function placeOrder(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'paymentMethod' => 'required|in:COD,Card',
            'cart' => 'required|array', // Array of cart items
            'totalAmount' => 'required|numeric', // Total amount of the order
        ]);

        // Begin a transaction to ensure atomic operations
        DB::beginTransaction();
        try {
            // Create the order
            $order = Order::create([
                'name' => $request->name,
                'address' => $request->address,
                'payment_method' => $request->paymentMethod,
                'total_amount' => $request->totalAmount,
            ]);

            // Save the cart items (order items)
            foreach ($request->cart as $item) {
                $order->items()->create([
                    'product_id' => $item['id'],  // Assuming you have a product ID
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Respond with success message
            return response()->json([
                'message' => 'Order placed successfully!',
                'order' => $order, // Return the order object as part of the response
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to place order. Please try again.',
            ], 500);
        }
    }
}
