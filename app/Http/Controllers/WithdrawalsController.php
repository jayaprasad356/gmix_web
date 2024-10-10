<?php

namespace App\Http\Controllers;

use App\Models\Withdrawals;
use App\Models\Staffs;
use Illuminate\Http\Request;

class WithdrawalsController extends Controller
{
    public function verify(Request $request)
    {
        $withdrawalIds = $request->input('withdrawal_ids', []);

        foreach ($withdrawalIds as $withdrawalId) {
            $withdrawal = Withdrawals::find($withdrawalId);
            if ($withdrawal) {
                // Update the withdrawal status to Paid (1)
                $withdrawal->status = 1;
                $withdrawal->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function index(Request $request)
    {
        $query = Withdrawals::with('staffs'); // Eager load staff relationship

        // Handle the search input
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhereHas('staffs', function ($q) use ($search) {
                      $q->where('name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%");
                  });
            });
        }

        // Filter by status
        $status = $request->input('status', 0); // Default to 0 (Pending)
        $query->where('status', $status);

        $withdrawals = $query->latest()->paginate(10); // Paginate the results

        return view('withdrawals.index', compact('withdrawals'));
    }
}
