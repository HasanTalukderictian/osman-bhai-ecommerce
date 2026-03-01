<?php

namespace App\Http\Controllers;

use App\Models\Couirer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouierController extends Controller
{
    /**
     * Display a listing of all courier settings.
     */
    public function index()
    {
        $couriers = Couirer::all();
        return response()->json([
            'status' => true,
            'data' => $couriers
        ]);
    }

    /**
     * Store a newly created courier setting in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'paperflyKey' => 'required|string|max:255',
            'Username'    => 'required|string|max:255',
            'Password'    => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create new record
        $courier = Couirer::create([
            'paperflyKey' => $request->paperflyKey,
            'Username'    => $request->Username,
            'Password'    => $request->Password,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Courier created successfully!',
            'data' => $courier
        ]);
    }

    /**
     * Remove the specified courier setting from storage.
     */
    public function destroy($id)
    {
        $courier = Couirer::find($id);

        if (!$courier) {
            return response()->json([
                'status' => false,
                'message' => 'Courier not found.'
            ], 404);
        }

        $courier->delete();

        return response()->json([
            'status' => true,
            'message' => 'Courier deleted successfully!'
        ]);
    }
}
