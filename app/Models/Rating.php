<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'price_rating', 'value_rating',
        'quality_rating', 'service_rating', 'title', 'feedback', 'image', 'customer_name'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
