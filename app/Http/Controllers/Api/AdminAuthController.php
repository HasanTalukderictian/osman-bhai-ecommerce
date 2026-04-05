<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Find admin user
        $user = User::where('email', $request->email)->first();

        // Check if user exists & password matches
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'data' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200); // HTTP 200
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials'
        ], 200); // HTTP 200 with JSON even on fail
    }
}
