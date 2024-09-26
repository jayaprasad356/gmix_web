<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use App\Models\Staffs;
use App\Models\Users;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Addresses;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index(Request $request)
    {
        // Eager load 'order' and the 'staff' related to that order
        $query = Tickets::with(['order.user.staff']); // Load the necessary relationships

        // Check if a status filter is set and apply it
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Check if a search term is present and apply it
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('order.user.staff', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Order by status, with status 0 first
        $query->orderByRaw('status = 0 DESC, status ASC');

        // Retrieve tickets with pagination
        $tickets = $query->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    public function edit(Tickets $ticket)
    {
        // Fetch related data from the order associated with the ticket
        $order = $ticket->order()->with(['user', 'product', 'addresses'])->first();

        // Return the data to the view
        return view('tickets.edit', compact('ticket', 'order')); // Pass ticket and order data to the view
    }

    public function update(Request $request, Tickets $ticket)
    {
        $ticket->status = $request->status;
        $ticket->save(); // Save the updated status to the database
    
        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Tickets $tickets)
    {
        $tickets->delete(); // Delete the ticket

        return response()->json([
            'success' => true
        ]);
    }
}
