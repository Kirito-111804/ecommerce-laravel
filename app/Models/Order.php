<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Define the table name if it differs from the default "orders"
    protected $table = 'orders';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'name',
        'address',
        'payment_method',
        'total_amount',
        'status',
    ];

    // Define the default attributes (optional)
    protected $attributes = [
        'status' => 'pending', // Default value for status
    ];

    // Define the data type for the total_amount field (optional)
    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // Define the relationship with the OrderItem model if needed
    // Assuming you have an order_items table for cart items
    public function items()
    {
        return $this->hasMany(OrderItem::class); // Adjust if you have a different model for order items
    }
}
