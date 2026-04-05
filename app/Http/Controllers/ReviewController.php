<?php

namespace App\Http\Controllers;

use App\Models\Reivew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
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
            $image->move(public_path('uploads/reviews'), $imageName);
            $imagePath = asset('uploads/reviews/' . $imageName);
        }

        $review = Reivew::create([
            'name' => $request->name,
            'designation' => $request->designation,
            'rating' => $request->rating,
            'comment' => $request->comment,
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
        $reviews = Reivew::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $reviews
        ], 200);
    }


     public function destroy($id)
    {
        $review = Reivew::find($id);

        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Delete image if exists
        if ($review->image && file_exists(public_path($review->image))) {
            unlink(public_path($review->image));
        }

        $review->delete();

        return response()->json([
            'status' => true,
            'message' => 'Review deleted successfully'
        ], 200);
    }

}
