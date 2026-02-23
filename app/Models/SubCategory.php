<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_category_id'];

    public function parentCategory()
    {
        return $this->belongsTo(ParentCategory::class);
    }

    public function products()
{
    return $this->hasMany(Product::class);
}
}
