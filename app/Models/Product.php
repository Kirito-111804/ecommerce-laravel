<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Ensure this is included
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory; // Add this trait

    // Define your fillable attributes
    protected $fillable = [
        'barcode',
        'description',
        'price',
        'quantity',
        'category',
    ];
}