<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;

class SupportTicketController extends Controller
{
    /**
     * Customer Ticket List
     */
    public function index(Request $request)
    {
        $tickets = SupportTicket::where(
            'customer_login_id',
            $request->user()->id
        )
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    /**
     * Create Ticket
     */
  public function store(Request $request)
{
    try {

        $request->validate([
            'subject' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'message' => 'required'
        ]);

        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized user'
            ], 401);
        }

        $ticket = SupportTicket::create([
            'customer_login_id' => $userId,
            'ticket_no' => 'TK-' . time(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'message' => $request->message,
            'status' => 'Pending'
        ]);

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Single Ticket Details
     */
    public function show(Request $request, $id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('customer_login_id', $request->user()->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    /**
     * Update Ticket
     */
    public function update(Request $request, $id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('customer_login_id', $request->user()->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        if ($ticket->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending tickets can be updated'
            ], 400);
        }

        $request->validate([
            'subject' => 'required|max:255',
            'category' => 'required|max:255',
            'priority' => 'required',
            'message' => 'required'
        ]);

        $ticket->update([
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully',
            'data' => $ticket
        ]);
    }

    /**
     * Delete Ticket
     */
    public function destroy(Request $request, $id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('customer_login_id', $request->user()->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully'
        ]);
    }

    /**
     * Admin Change Status
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Resolved,Closed'
        ]);

        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
