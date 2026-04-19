<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ParentCategory;
use Illuminate\Http\Request;

class ParentCategoryController extends Controller
{
    //

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('categories', 'public');
    }

    $category = ParentCategory::create([
        'name' => $request->name,
        'image' => $imagePath
    ]);

    return response()->json([
        'status' => 'success',
        'data' => [
            'id' => $category->id,
            'name' => $category->name,
            'image_url' => $category->image ? asset('storage/' . $category->image) : null,
        ]
    ]);
}

 public function destroy($id)
{
    // Find category with subcategories and their products
    $category = ParentCategory::with('subCategories.products')->find($id);

    if (!$category) {
        return response()->json([
            'status' => 'error',
            'message' => 'Category not found'
        ], 404);
    }

    // Check if any products exist in subcategories
    foreach ($category->subCategories as $sub) {
        if ($sub->products()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category. Please delete products under this category first.'
            ], 400);
        }
    }

    // Delete all subcategories
    foreach ($category->subCategories as $sub) {
        $sub->delete();
    }

    // Delete parent category
    $category->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Category and its subcategories deleted successfully'
    ]);
}

}
