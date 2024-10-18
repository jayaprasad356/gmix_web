<?php
namespace App\Http\Controllers;

use App\Models\StaffTransactions;
use App\Models\Staffs;
use Illuminate\Http\Request;

class StaffTransactionsController extends Controller
{
    public function index(Request $request)
    {
        // Eager load the staff relationship
        $query = StaffTransactions::query()->with('staff');
    
        // Filter by search term if it exists in the request
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhereHas('staff', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");  // Corrected to 'staff'
                      });
            });
        }
    
        // Filter by staff if it exists in the request
        if ($request->has('staff_id') && $request->staff_id != '') {
            $query->where('staff_id', $request->staff_id);
        }
    
        // Paginate the results
        $staff_transactions = $query->latest()->paginate(10);
    
        // Append query parameters to the pagination links
        $staff_transactions->appends($request->except('page'));
    
        // Fetch all staff for the filter dropdown
        $staffs = Staffs::all();
    
        // Pass transactions and staff to the view
        return view('staff_transactions.index', compact('staff_transactions', 'staffs'));
    }
    

    public function destroy(StaffTransactions $staff_transaction)
    {
        $staff_transaction->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}

