<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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




public function socialLogin(Request $request)
{
    try {
        // 1. Validation with better error messages
        $validated = $request->validate([
            'provider' => 'required|in:google,facebook',
            'provider_id' => 'required|string',
            'email' => 'nullable|email',
            'name' => 'required|string',
            'avatar' => 'nullable|string|url' // Validate avatar is valid URL
        ]);

        Log::info('Social Login Request:', $request->all()); // Debug log

        // 2. Check existing user by provider_id
        $user = CustomerLogin::where('provider_id', $request->provider_id)->first();

        if (!$user) {
            // 3. Check if user exists with same email (optional but recommended)
            if ($request->email) {
                $existingUser = CustomerLogin::where('email', $request->email)->first();
                if ($existingUser) {
                    // Update existing user with provider info
                    $existingUser->update([
                        'provider' => $request->provider,
                        'provider_id' => $request->provider_id,
                        'avatar' => $request->avatar ?? $existingUser->avatar,
                    ]);
                    $user = $existingUser;
                }
            }

            // 4. Create new user if not exists
            if (!$user) {
                $nameParts = explode(' ', $request->name, 2); // Limit to 2 parts

                $user = CustomerLogin::create([
                    'first_name' => $nameParts[0] ?? '',
                    'last_name' => $nameParts[1] ?? '',
                    'email' => $request->email,
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                    'avatar' => $request->avatar ?? null,
                    // Add any other required fields with default values
                    'password' => null, // If password field is required, set null or random
                    'phone' => null, // If phone is required
                    'status' => 'active', // If status column exists
                ]);
            }
        } else {
            // 5. Update existing user with latest info
            $user->update([
                'avatar' => $request->avatar ?? $user->avatar,
                'last_login_at' => now(), // If you have this field
            ]);
        }

        // 6. Check if user was created/found successfully
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User creation or retrieval failed'
            ], 500);
        }

        // 7. Create token with abilities
        $token = $user->createToken('customer_token', ['*'])->plainTextToken;

        // 8. Return success response
        return response()->json([
            'status' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation Error:', $e->errors());
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        // Log the actual error for debugging
        Log::error('Social Login Error:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}


}

