<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    //

     public function register(Request $request)
    {
        $request->validate([
            'firstName' => 'required',
            'lastName' => 'nullable',
            'email' => 'nullable|email|unique:customer_logins,email',
            'phone' => 'required|unique:customer_logins,phone',
            'password' => 'required|min:6',
        ]);

        $user = CustomerLogin::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'user' => $user
        ]);
    }

    // ✅ LOGIN


    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'phone' => 'required',
    //         'password' => 'required'
    //     ]);

    //     $user = CustomerLogin::where('phone', $request->phone)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid phone or password'
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login successful',
    //         'user' => $user
    //     ]);
    // }

    public function login(Request $request)
{
    $request->validate([
        'phone' => 'required',
        'password' => 'required'
    ]);

    $user = CustomerLogin::where('phone', $request->phone)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid phone or password'
        ]);
    }

    // ✅ create token
    $token = $user->createToken('customer_token')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token   // 🔥 important
    ]);
}

    // ✅ LOGOUT
    public function logout()
    {
        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ]);
    }

public function profile(Request $request)
{
    try {
        $user = $request->user(); // 🔥 Sanctum auto user detect

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'data' => $user
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}


}

