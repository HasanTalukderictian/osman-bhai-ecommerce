<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'address',
        'district',
        'thana',
        'total_price',
        'delivery_charge',
        'final_total',
        'cart_items'

    ];

    protected $casts = [
        'cart_items' => 'array',
    ];
   public function items()
{
    return $this->hasMany(OrderItem::class);
}
}
