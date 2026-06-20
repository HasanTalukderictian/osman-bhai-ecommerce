<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Rating;
use Dotenv\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // =========================
    // Store Product
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'rating' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_category_id' => 'required|exists:parent_categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            // store in storage/app/public/products
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'rating' => $request->rating,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image' => $imagePath,
            'parent_category_id' => $request->parent_category_id,
            'sub_category_id' => $request->sub_category_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product,
        ]);
    }

    // =========================
    // List All Products
    // =========================
    public function index()
    {
        $products = Product::with(['parentCategory', 'subCategory'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'rating' => $product->rating,
                    'quantity' => $product->quantity,
                    'description' => $product->description,
                    'parent_category' => $product->parentCategory?->name,
                    'sub_category' => $product->subCategory?->name,
                    'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                ];
            });

        return response()->json($products);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Delete image from storage if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

  public function productsBySubcategory($parent, $subcategory)
{
    $subcategoryName = str_replace('-', ' ', $subcategory);

    $products = Product::with(['parentCategory', 'subCategory', 'images'])
        ->whereHas('parentCategory', function($q) use ($parent) {
            $q->whereRaw('LOWER(name) = ?', [strtolower($parent)]);
        })
        ->whereHas('subCategory', function($q) use ($subcategoryName) {
            $q->whereRaw('LOWER(name) = ?', [strtolower($subcategoryName)]);
        })
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'rating' => $product->rating,
                'quantity' => $product->quantity,
                'description' => $product->description,
                'parent_category' => $product->parentCategory?->name,
                'sub_category' => $product->subCategory?->name,
                'images' => $product->images->map(function ($img) {
                    return asset('storage/' . $img->image_path);
                })->toArray(),
                // first image for backward compatibility (optional)
                'image_url' => $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : null,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $products,
    ]);
}




    public function update(Request $request, $id)
    {
        $product = Product::find($id);

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_category_id' => 'required|exists:parent_categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        // Update other fields
        $product->name = $request->name;
        $product->price = $request->price;
        $product->rating = $request->rating;
        $product->quantity = $request->quantity;
        $product->description = $request->description;
        $product->parent_category_id = $request->parent_category_id;
        $product->sub_category_id = $request->sub_category_id;

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'rating' => $product->rating,
                'quantity' => $product->quantity,
                'description' => $product->description,
                'parent_category_id' => $product->parent_category_id,
                'sub_category_id' => $product->sub_category_id,
                'image_url' => $product->image ? asset('storage/' . $product->image) : null,
            ]
        ]);
    }


    public function productsByParentCategory($parent)
{
    $parentName = str_replace('-', ' ', $parent);

    $products = Product::with(['parentCategory', 'subCategory', 'images'])
        ->whereHas('parentCategory', function ($q) use ($parentName) {
            $q->whereRaw('LOWER(name) = ?', [strtolower($parentName)]);
        })
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'rating' => $product->rating,
                'quantity' => $product->quantity,
                'description' => $product->description,
                'parent_category' => $product->parentCategory?->name,
                'sub_category' => $product->subCategory?->name,
                'images' => $product->images->map(function ($img) {
                    return asset('storage/' . $img->image_path);
                }),
                'image_url' => $product->images->first()
                    ? asset('storage/' . $product->images->first()->image_path)
                    : null,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $products
    ]);
}


public function storeRating(Request $request)
    {
        try {
            Log::info('Rating submission request:', $request->all());

            // Simple validation using request()->validate()
            $validated = $request->validate([
                'product_id'     => 'required|exists:products,id',
                'price_rating'   => 'required|integer|between:1,5',
                'value_rating'   => 'required|integer|between:1,5',
                'quality_rating' => 'required|integer|between:1,5',
                'service_rating' => 'required|integer|between:1,5',
                'customer_name'  => 'required|string|max:255',
                'title'          => 'required|string|max:255',
                'feedback'       => 'required|string',
                'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            $imagePath = null;

            // Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();

                // Create directory if it doesn't exist
                $uploadPath = public_path('uploads/reviews');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $filename);
                $imagePath = 'uploads/reviews/' . $filename;
            }

            // Create rating
            $rating = Rating::create([
                'product_id'     => $validated['product_id'],
                'price_rating'   => $validated['price_rating'],
                'value_rating'   => $validated['value_rating'],
                'quality_rating' => $validated['quality_rating'],
                'service_rating' => $validated['service_rating'],
                'title'          => $validated['title'],
                'customer_name'  => $validated['customer_name'],
                'feedback'       => $validated['feedback'],
                'image'          => $imagePath,
            ]);

            Log::info('Rating created successfully:', ['rating_id' => $rating->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Review submitted successfully!',
                'data' => $rating
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error in storeRating:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }


public function show($id)
{
    try {
        // Load product with relationships
        $product = Product::with(['parentCategory', 'subCategory', 'images', 'ratings'])
            ->find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Calculate average rating
        $avgRating = $product->ratings->avg('price_rating') ?? 0;

        // Format response
        return response()->json([
            'status' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'rating' => $product->rating,
                'quantity' => $product->quantity,
                'description' => $product->description,
                'parent_category' => $product->parentCategory?->name,
                'sub_category' => $product->subCategory?->name,
                'subcategory_id' => $product->sub_category_id,
                'avg_rating' => round($avgRating, 1),
                'images' => $product->images->map(function ($img) {
                    return asset('storage/' . $img->image_path);
                })->toArray(),
                // Fallback for single image (if your old system uses image field)
                'image' => $product->image ? asset('storage/' . $product->image) : null,
                // Reviews/ratings
                'reviews' => $product->ratings->map(function ($rating) {
                    return [
                        'id' => $rating->id,
                        'customer_name' => $rating->customer_name,
                        'title' => $rating->title,
                        'feedback' => $rating->feedback,
                        'price_rating' => $rating->price_rating,
                        'value_rating' => $rating->value_rating,
                        'quality_rating' => $rating->quality_rating,
                        'service_rating' => $rating->service_rating,
                        'image' => $rating->image ? asset($rating->image) : null,
                        'created_at' => $rating->created_at,
                    ];
                }),
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error fetching product: ' . $e->getMessage()
        ], 500);
    }
}



}
