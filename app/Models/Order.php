<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

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

    // Relationship with OrderItem model
    public function items()
    {
        return $this->hasMany(OrderItem::class); // Each order has many items
    }
}
