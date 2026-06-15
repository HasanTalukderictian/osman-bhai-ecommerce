<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ParentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParentCategoryController extends Controller
{
    //

 public function store(Request $request)
    {
        try {
            // Log the request for debugging
            Log::info('Category store request:', $request->all());

            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $imagePath = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Check if file is valid
                if (!$file->isValid()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid image file'
                    ], 400);
                }

                // Store the image
                $imagePath = $file->store('categories', 'public');

                if (!$imagePath) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to store image'
                    ], 500);
                }
            }

            $category = ParentCategory::create([
                'name' => $request->name,
                'image' => $imagePath
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Category created successfully',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image_url' => $imagePath ? asset('storage/' . $imagePath) : null,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Category creation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'status' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
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
