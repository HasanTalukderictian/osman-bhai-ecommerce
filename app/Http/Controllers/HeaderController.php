<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Headers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HeaderController extends Controller
{
    //

    public function store(Request $request)
{
    // Validate input
    $request->validate([
        'Companyname' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    try {
        // Check duplicate Companyname
        $existing = Headers::where('Companyname', $request->Companyname)->first();
        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'Company Name already exists!'
            ], 409); // 409 Conflict
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/Header'), $imageName);
            $imagePath = url('uploads/Header/' . $imageName);
        } else {
            $imagePath = null;
        }

        // Create header entry
        $headers = Headers::create([
            'Companyname' => $request->Companyname,
            'image' => $imagePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Header Setting added successfully',
            'data' => $headers
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to add header',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function index()
    {
        $headers = Headers::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $headers
        ], 200);
    }

     public function destroy($id)
    {
        $header = Headers::find($id);

        if (!$header) {
            return response()->json([
                'status' => false,
                'message' => 'Header not found'
            ], 404);
        }

        // Delete image if exists
        if ($header->image) {
            $imagePath = public_path(parse_url($header->image, PHP_URL_PATH));
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $header->delete();

        return response()->json([
            'status' => true,
            'message' => 'Header deleted successfully'
        ], 200);
    }

}
