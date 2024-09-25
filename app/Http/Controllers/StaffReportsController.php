<?php

namespace App\Http\Controllers;

use App\Models\Staffs;
use App\Models\Orders;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StaffReportsController extends Controller
{
    public function index(Request $request)
    {
        // Determine the date range filter, default to 'today' if not provided
        $dateFilter = $request->input('date_filter', 'today');
        $searchQuery = $request->input('search');  // Get the search input from the user
        $startDate = null;
        $endDate = Carbon::today(); // Default to today for end date

        // Set date ranges based on the selected filter
        switch ($dateFilter) {
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday();
                break;
            case 'last_7_days':
                $startDate = Carbon::today()->subDays(7);
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'today':
            default:
                $startDate = Carbon::today();
                break;
        }

        // Fetch staff data along with their associated user and count of orders in the date range
        $staffs = Staffs::query()
            ->select(
                'staffs.id',
                'staffs.name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(CASE WHEN orders.payment_mode = "COD" THEN 1 ELSE 0 END) as cod_orders'),
                DB::raw('SUM(CASE WHEN orders.payment_mode = "Prepaid" THEN 1 ELSE 0 END) as prepaid_orders')
            )
            ->leftJoin('users', 'users.staff_id', '=', 'staffs.id') // Assuming staff_id is the foreign key in users table
            ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                $join->on('orders.user_id', '=', 'users.id') // Assuming orders table has user_id
                    ->whereBetween('orders.ordered_date', [$startDate, $endDate]); // Filter orders based on date range
            });

        // Apply search filter if searchQuery is provided
        if ($searchQuery) {
            $staffs->where('staffs.name', 'like', '%' . $searchQuery . '%');
        }

        // Group by staff ID and name, and paginate the results
        $staffs = $staffs->groupBy('staffs.id', 'staffs.name')
                         ->paginate(10);

        // Fetch users for dropdown (if needed)
        $users = Users::all();

        // Pass staff, user data, and the current search query to the view
        return view('staff_reports.index', compact('staffs', 'users', 'searchQuery'));
    }
}
