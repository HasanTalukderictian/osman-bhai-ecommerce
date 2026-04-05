<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    /**
     * Display a listing of the stores.
     */
    public function index(): JsonResponse
    {
        try {
            $stores = Store::all();
            return response()->json([
                'status' => true,
                'data' => $stores
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch stores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created store in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'district_name' => 'required|string|max:255',
                'thana_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'label' => 'required|in:Home,Shop'
            ]);

            $store = Store::create([
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'district_name' => $request->district_name,
                'thana_name' => $request->thana_name,
                'address' => $request->address,
                'label' => $request->label
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Store created successfully',
                'data' => $store
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create store',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
