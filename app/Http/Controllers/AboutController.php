<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\File;

class AboutController extends Controller
{
    //

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['title', 'description']);

        // 2. Image Upload Logic
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/about'), $filename);
            $data['image'] = 'uploads/about/' . $filename;
        }

        // 3. Save to Database
        $about = About::create($data);

        return response()->json([
            'success' => true,
            'message' => 'About information saved successfully!',
            'data'    => $about
        ], 201);
    }

    public function index()
    {
        try {
            // Database theke sob data latest order-e niye asha
            $data = About::orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'data'    => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $about = About::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['title', 'description']);

        // Image Update Logic
        if ($request->hasFile('image')) {
            // 1. Purono image thakle seta delete kora
            if ($about->image && File::exists(public_path($about->image))) {
                File::delete(public_path($about->image));
            }

            // 2. Notun image upload
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/about'), $filename);
            $data['image'] = 'uploads/about/' . $filename;
        }

        $about->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Information updated successfully!',
            'data'    => $about
        ], 200);
    }

    public function destroy($id)
    {
        $about = About::findOrFail($id);

        // Database row delete korar age image file-ti delete kora
        if ($about->image && File::exists(public_path($about->image))) {
            File::delete(public_path($about->image));
        }

        $about->delete();

        return response()->json([
            'success' => true,
            'message' => 'Information deleted successfully!'
        ], 200);
    }

}
