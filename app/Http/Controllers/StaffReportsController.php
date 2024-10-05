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
            case 'this_week':
                    // This will give you the current week's start (Sunday) and end (Saturday)
                $startDate = Carbon::now()->startOfWeek(Carbon::SUNDAY);
                $endDate = Carbon::now()->endOfWeek(Carbon::SATURDAY);
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'today':
            default:
                $startDate = Carbon::today();
                break;
        }

        $staffs = Staffs::query()
                ->select(
                    'staffs.id',
                    'staffs.name',
                    // Only count orders where the status is NOT 2
                    DB::raw('COUNT(CASE WHEN orders.status != 2 THEN orders.id ELSE NULL END) as total_orders'),
                    DB::raw('SUM(CASE WHEN orders.payment_mode = "COD" AND orders.status != 2 THEN 1 ELSE 0 END) as cod_orders'),
                    DB::raw('SUM(CASE WHEN orders.payment_mode = "Prepaid" AND orders.status != 2 THEN 1 ELSE 0 END) as prepaid_orders'),
                    // Only sum grams where the order status is NOT 2
                    DB::raw('SUM(CASE WHEN orders.status != 2 THEN products.measurement ELSE 0 END) as total_grams') // Only sum grams where status != 2
                )
                ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                    $join->on('orders.staff_id', '=', 'staffs.id') // Now using staff_id from orders table
                        ->whereBetween('orders.ordered_date', [$startDate, $endDate]); // Filter orders based on date range
                })
                ->leftJoin('products', 'orders.product_id', '=', 'products.id'); // Join with products table using product_id


        // Group by staff ID and name, and paginate the results
        $staffs = $staffs->groupBy('staffs.id', 'staffs.name')
                         ->paginate(10);

        // Calculate total quantity for all staff (in grams)
        $totalGrams = $staffs->sum('total_grams');
        $totalKg = $totalGrams / 1000;  // Convert grams to kilograms

        // Fetch users for dropdown (if needed)
        $users = Users::all();

        // Pass staff, user data, the total quantity, and the current search query to the view
        return view('staff_reports.index', compact('staffs', 'users', 'searchQuery', 'totalKg'));
    }
}
