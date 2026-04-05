<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    //

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'parent_category_id' => 'required|exists:parent_categories,id'
    ]);

    $sub = SubCategory::create([
        'name' => $request->name,
        'parent_category_id' => $request->parent_category_id
    ]);

    return response()->json([
        'status' => 'success',
        'data' => $sub
    ]);
}

public function destroy($id)
{
    // Find the subcategory
    $subCategory = SubCategory::with('products')->find($id);

    if (!$subCategory) {
        return response()->json([
            'status' => 'error',
            'message' => 'SubCategory not found'
        ], 404);
    }

    // Check if any products exist
    if ($subCategory->products()->count() > 0) {
        return response()->json([
            'status' => 'error',
            'message' => 'Cannot delete subcategory. Please delete products under this subcategory first.'
        ], 400);
    }

    // Delete subcategory
    $subCategory->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'SubCategory deleted successfully'
    ]);
}


}
