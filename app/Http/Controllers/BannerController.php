<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banners;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    //

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'price' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banners'), $imageName);
            $imagePath = asset('uploads/banners/' . $imageName);
        }

        $review = Banners::create([
            'title' => $request->title,
            'price' => $request->price,
            'image' => $imagePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully',
            'data' => $review
        ], 201);
    }



     public function index()
    {
        $banners = Banners::all(); // or paginate if needed: Banners::paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Banners fetched successfully',
            'data' => $banners
        ]);
    }

    // Delete a banner by ID
    public function destroy($id)
    {
        $banner = Banners::find($id);

        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Banner not found'
            ], 404);
        }

        // Delete banner image from server if exists
        if ($banner->image) {
            $imagePath = public_path(parse_url($banner->image, PHP_URL_PATH));
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        $banner->delete();

        return response()->json([
            'status' => true,
            'message' => 'Banner deleted successfully'
        ]);
    }
}
