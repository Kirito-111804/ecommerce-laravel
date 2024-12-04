<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    // Relationship with Order model
    public function order()
    {
        return $this->belongsTo(Order::class); // Each item belongs to an order
    }

    // Relationship with Product model (if you have a Product model)
    public function product()
    {
        return $this->belongsTo(Product::class); // Each item belongs to a product
    }
}
