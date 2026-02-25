<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserInfoController extends Controller
{
    //


     public function index()
    {
        $userInfo = UserInfo::all();

        return response()->json([
            'data' => $userInfo
        ]);
    }

public function update(Request $request, $id)
{
    $request->validate([
        'CompanyName' => 'required|string|max:255',
        'YourName' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $userInfo = UserInfo::find($id);

    if (!$userInfo) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    $imagePath = $userInfo->image;

    // image upload handling
    if ($request->hasFile('image')) {
        // delete old image
        if ($imagePath && Storage::exists('public/' . $imagePath)) {
            Storage::delete('public/' . $imagePath);
        }

        $imagePath = $request->file('image')->store('user_images', 'public');
    }

    $userInfo->update([
        'company_name' => $request->CompanyName,
        'your_name' => $request->YourName,
        'image' => $imagePath
    ]);

    return response()->json([
        'message' => 'User info updated successfully',
        'data' => $userInfo
    ]);
}



}
