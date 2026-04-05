<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    //

   public function store(Request $request)
{
    // 🔴 Check if already exists
    if (Contact::count() > 0) {
        return response()->json([
            'status' => false,
            'message' => 'Contact information already exists. You can only update the existing record.'
        ], 409); // 409 = Conflict
    }

    $validator = Validator::make($request->all(), [
        'address' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'required|string|max:20',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    $imagePath = null;

    if ($request->hasFile('image')) {

        if (!file_exists(public_path('uploads/contacts'))) {
            mkdir(public_path('uploads/contacts'), 0755, true);
        }

        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('uploads/contacts'), $imageName);

        $imagePath = asset('uploads/contacts/' . $imageName);
    }

    $contact = Contact::create([
        'address' => $request->address,
        'email' => $request->email,
        'phone' => $request->phone,
        'image' => $imagePath,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Contact information saved successfully',
        'data' => $contact
    ], 201);
}

  public function update(Request $request, $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            if (!file_exists(public_path('uploads/contacts'))) {
                mkdir(public_path('uploads/contacts'), 0755, true);
            }

            // Delete old image if exists
            if ($contact->image) {
                $oldImagePath = str_replace(asset(''), public_path(''), $contact->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/contacts'), $imageName);
            $contact->image = asset('uploads/contacts/' . $imageName);
        }

        $contact->address = $request->address;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->save();

        return response()->json([
            'status' => true,
            'message' => 'Contact updated successfully',
            'data' => $contact
        ], 200);
    }


     public function destroy($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        // Delete image if exists
        if ($contact->image) {
            $oldImagePath = str_replace(asset(''), public_path(''), $contact->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $contact->delete();

        return response()->json([
            'status' => true,
            'message' => 'Contact deleted successfully'
        ], 200);
    }


    // 🔹 Get Contact Information
public function index()
{
    $contact = Contact::first(); // Get the first (and only) contact record

    if (!$contact) {
        return response()->json([
            'status' => false,
            'message' => 'No contact information found'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $contact
    ], 200);
}

}
