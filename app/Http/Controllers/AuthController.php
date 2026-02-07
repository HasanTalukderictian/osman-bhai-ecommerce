<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
//    public function login(Request $request)
// {
//     // Validate input
//     $request->validate([
//         'email' => 'required|email',
//         'password' => 'required',
//     ]);

//     // Find user
//     $user = User::where('email', $request->email)->first();

//     if (!$user || !Hash::check($request->password, $user->password)) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Invalid credentials'
//         ], 401);
//     }

//     // Create Sanctum token
//     $token = $user->createToken('authToken')->plainTextToken;

//     return response()->json([
//         'status' => true,
//         'message' => 'Login successful',
//         'data' => [
//             'token' => $token,
//             'user' => $user
//         ]
//     ]);
// }





    // public function logout(Request $request)
    // {
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'message' => 'Logged out successfully'
    //     ]);
    // }





public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    // ✅ Active check
    if (!$user->active) {
        return response()->json([
            'status' => false,
            'message' => 'Your account is inactive. Please contact admin.'
        ], 403);
    }

    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'data' => [
            'token' => $token,
            'user' => $user
        ]
    ]);
}





}
