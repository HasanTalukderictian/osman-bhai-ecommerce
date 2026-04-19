<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ParentCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class AllCategoryController extends Controller
{
    //
   public function index()
{
    $categories = ParentCategory::with('subCategories')
        ->get()
        ->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'image' => $cat->image,
                'image_url' => $cat->image ? asset('storage/' . $cat->image) : null,

                'sub_categories' => $cat->subCategories->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'parent_category_id' => $sub->parent_category_id,
                        'created_at' => $sub->created_at,
                        'updated_at' => $sub->updated_at,
                    ];
                })
            ];
        });

    return response()->json([
        'success' => true,
        'message' => 'All categories fetched successfully',
        'data' => $categories,
    ]);
}
}
