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
        return $this->belongsTo(ParentCategory::class);
    }

    // Sub Category Relation
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function images()
{
    return $this->hasMany(ProductImage::class);
}
}
