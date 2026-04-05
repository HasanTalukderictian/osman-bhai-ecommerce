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
        // 1. Parent Category সহ SubCategory
        $categories = ParentCategory::with('subCategories')->get();

        // Optionally: SubCategory standalone
        $subcategories = SubCategory::all();

        return response()->json([
            'success' => true,
            'message' => 'All categories fetched successfully',
            'data' => $categories,
            'subcategories' => $subcategories,
        ]);
    }
}
