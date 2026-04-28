<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    // 1. Sob data fetch korar jonno (Read)
    public function index()
    {
        $contents = Content::orderBy('created_at', 'desc')->get();
        return response()->json($contents);
    }

    // Store function (Already thaka)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'section'     => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_visible'  => 'required|boolean',
        ]);

        $content = Content::create($validated);

        return response()->json([
            'message' => 'Data saved successfully!',
            'data'    => $content
        ], 201);
    }

    // 2. Data update korar jonno (Update)
    public function update(Request $request, $id)
    {
        $content = Content::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'section'     => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_visible'  => 'sometimes|required|boolean',
        ]);

        $content->update($validated);

        return response()->json([
            'message' => 'Data updated successfully!',
            'data'    => $content
        ]);
    }

    // 3. Data delete korar jonno (Delete)
    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $content->delete();

        return response()->json([
            'message' => 'Data deleted successfully!'
        ]);
    }
}
