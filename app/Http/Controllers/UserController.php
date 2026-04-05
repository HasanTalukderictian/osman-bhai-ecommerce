<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function createUser(Request $request)
    {
        $authUser = auth()->user();

        if (!$authUser->isSuperAdmin()) {
            return response()->json([
                'message' => 'Only super admin can create users'
            ], 403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role' => 'required'
        ]);

        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json(['message' => 'Invalid role']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function getAllUsers()
    {
        $authUser = auth()->user();

        if (!$authUser->isSuperAdmin()) {
            return response()->json([
                'message' => 'Only super admin can view users'
            ], 403);
        }

        // Eager load role relation
        $users = User::with('role')->get();

        return response()->json([
            'message' => 'Users fetched successfully',
            'users' => $users
        ]);
    }


    public function toggleStatus(Request $request, $id)
    {
        $authUser = auth()->user();

        // Only super admin can toggle user status
        if (!$authUser->isSuperAdmin()) {
            return response()->json([
                'message' => 'Only super admin can update user status'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Toggle status based on request body
        $request->validate([
            'active' => 'required|boolean'
        ]);

        $user->active = $request->active;
        $user->save();

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user
        ]);
    }
}
