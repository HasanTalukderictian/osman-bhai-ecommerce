<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'rating',
        'quantity',
        'description',
        'image',
        'parent_category_id',
        'sub_category_id',
    ];

    // Parent Category Relation
    public function parentCategory()
    {
        return $this->belongsTo(ParentCategory::class, 'parent_category_id');
    }

    // Sub Category Relation
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    // Product Images Relation
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Rating/Review Relation - শুধুমাত্র একটি রাখুন
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'product_id');
    }

    // যদি reviews নামে রিলেশন চান তবে aliases করুন
    public function reviews()
    {
        return $this->ratings(); // Same as ratings
    }
}
