<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    /**
     * Store a newly created team member in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/team'), $imageName);
                $imagePath = url('uploads/team/' . $imageName);
            } else {
                $imagePath = null;
            }

            // Create team member
            $team = Team::create([
                'name' => $request->name,
                'designation' => $request->designation,
                'image' => $imagePath,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Team member added successfully',
                'data' => $team
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add team member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   public function destroy($id)
{
    try {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'status' => false,
                'message' => 'Team member not found'
            ], 404);
        }

        // Delete image if exists
        if ($team->image) {

            // Convert URL to relative path
            $imagePath = str_replace(url('/'), '', $team->image);

            $fullPath = public_path($imagePath);

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        // Delete record
        $team->delete();

        return response()->json([
            'status' => true,
            'message' => 'Team member deleted successfully'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to delete team member',
            'error' => $e->getMessage()
        ], 500);
    }
}

   public function index()
    {
        $team = Team::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $team
        ], 200);
    }

}
