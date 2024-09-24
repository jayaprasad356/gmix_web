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

    // Search functionality (if required)
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->whereHas('order.user.staff', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%"); // Search by staff name
        })->orWhere('title', 'like', "%{$search}%");
    }

    // Paginate the results
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
    
    public function destroy(Tickets $ticket)
    {
        $ticket->delete(); // Delete the ticket

        return response()->json([
            'success' => true
        ]);
    }
}
