<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    //

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'rating' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'description' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_category_id' => 'required|exists:parent_categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        // Create product first
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'rating' => $request->rating,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'parent_category_id' => $request->parent_category_id,
            'sub_category_id' => $request->sub_category_id,
        ]);

        // Upload multiple images
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {

                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product->load('images')
        ]);
    }

    // public function index()
    // {
    //     $products = Product::with(['parentCategory', 'subCategory', 'images'])
    //         ->orderBy('id', 'desc')
    //         ->get()
    //         ->map(function ($product) {

    //             return [
    //                 'id' => $product->id,
    //                 'name' => $product->name,
    //                 'price' => $product->price,
    //                 'images' => $product->images->map(function ($img) {
    //                     return asset('storage/' . $img->image_path);
    //                 }),
    //                 'parent_category' => $product->parentCategory?->name,
    //                 'sub_category' => $product->subCategory?->name,
    //                   'rating' => $product->rating,
    //                     'quantity' => $product->quantity,
    //                      'description' => $product->description,
    //             ];
    //         });

    //     return response()->json($products);
    // }


public function index()
{
    try {
        $products = Product::with(['parentCategory', 'subCategory', 'images', 'reviews'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($product) {

                // Safe average rating calculation
                $avgRating = 0;
                if ($product->reviews && $product->reviews->count() > 0) {
                    $totalRating = 0;
                    $reviewCount = 0;

                    foreach ($product->reviews as $review) {
                        $totalRating += (
                            ($review->price_rating ?? 0) +
                            ($review->value_rating ?? 0) +
                            ($review->quality_rating ?? 0) +
                            ($review->service_rating ?? 0)
                        ) / 4;
                        $reviewCount++;
                    }

                    $avgRating = $totalRating / $reviewCount;
                }

                // Safe images handling
                $imageUrls = [];
                if ($product->images && $product->images->count() > 0) {
                    foreach ($product->images as $img) {
                        if ($img && $img->image_path) {
                            $imageUrls[] = asset('storage/' . $img->image_path);
                        }
                    }
                }

                // Safe reviews handling
                $reviewData = [];
                if ($product->reviews && $product->reviews->count() > 0) {
                    foreach ($product->reviews as $review) {
                        $reviewData[] = [
                            'id' => $review->id ?? null,
                            'title' => $review->title ?? '',
                            'customer_name' => $review->customer_name ?? 'Anonymous',
                            'feedback' => $review->feedback ?? '',
                            'price_rating' => $review->price_rating ?? 0,
                            'value_rating' => $review->value_rating ?? 0,
                            'quality_rating' => $review->quality_rating ?? 0,
                            'service_rating' => $review->service_rating ?? 0,
                            'image' => $review->image ? asset($review->image) : null,
                            'created_at' => $review->created_at ? $review->created_at->format('d M Y') : date('d M Y'),
                        ];
                    }
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name ?? '',
                    'price' => $product->price ?? 0,
                    'rating' => round($avgRating, 1) ?: 0,
                    'quantity' => $product->quantity ?? 0,
                    'description' => $product->description ?? '',
                    'parent_category_id' => $product->parent_category_id,
                    'sub_category_id' => $product->sub_category_id,
                    'parent_category' => $product->parentCategory?->name ?? '',
                    'sub_category' => $product->subCategory?->name ?? '',
                    'images' => $imageUrls,
                    'reviews' => $reviewData,
                    'created_at' => $product->created_at ? $product->created_at->format('d M Y') : date('d M Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products fetched successfully'
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Product fetch error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch products: ' . $e->getMessage(),
            'data' => []
        ], 500);
    }
}

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false], 404);
        }

        // delete images from storage
        foreach ($product->images as $image) {

            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }



    public function update(Request $request, $id)
    {
        $product = Product::with('images')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'rating' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'description' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_category_id' => 'required|exists:parent_categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'delete_image_ids' => 'nullable|array', // ids of images to delete
            'delete_image_ids.*' => 'integer|exists:product_images,id',
        ]);

        // Update product fields
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'rating' => $request->rating,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'parent_category_id' => $request->parent_category_id,
            'sub_category_id' => $request->sub_category_id,
        ]);

        // Delete selected images
        if ($request->filled('delete_image_ids')) {
            $imagesToDelete = ProductImage::whereIn('id', $request->delete_image_ids)
                ->where('product_id', $product->id)
                ->get();

            foreach ($imagesToDelete as $img) {
                if (Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $img->delete();
            }
        }

        // Upload new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product->load('images')
        ]);
    }
}
