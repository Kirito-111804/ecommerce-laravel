<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Specify the table name if it’s not the plural of the model name
    protected $table = 'carts';

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price',
    ];

    /**
     * Get the product associated with the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that owns the cart item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
