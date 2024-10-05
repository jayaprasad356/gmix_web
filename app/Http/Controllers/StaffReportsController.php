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

        // Get the start and end of the current week (Sunday to Saturday)
        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = Carbon::now()->endOfWeek(Carbon::SATURDAY);

        $staffs = Staffs::query()
            ->select(
                'staffs.id',
                'staffs.name',
                // Count total orders excluding status = 2
                DB::raw('COUNT(CASE WHEN orders.status != 2 THEN orders.id ELSE NULL END) as total_orders'),
                DB::raw('SUM(CASE WHEN orders.payment_mode = "COD" AND orders.status != 2 THEN 1 ELSE 0 END) as cod_orders'),
                DB::raw('SUM(CASE WHEN orders.payment_mode = "Prepaid" AND orders.status != 2 THEN 1 ELSE 0 END) as prepaid_orders'),
                DB::raw('SUM(CASE WHEN orders.status != 2 THEN products.measurement ELSE 0 END) as total_grams'), // Total grams (excluding status = 2)
                // Weekly quantity (Sunday to Saturday) filtering by order date within the week range
                DB::raw('SUM(CASE WHEN orders.ordered_date BETWEEN "' . $weekStart . '" AND "' . $weekEnd . '" AND orders.status != 2 THEN products.measurement ELSE 0 END) as weekly_grams') // Week grams
            )
            ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                $join->on('orders.staff_id', '=', 'staffs.id')
                    ->whereBetween('orders.ordered_date', [$startDate, $endDate]); // Filter based on selected date range
            })
            ->leftJoin('products', 'orders.product_id', '=', 'products.id');

        // Group by staff ID and name
        $staffs = $staffs->groupBy('staffs.id', 'staffs.name')
                         ->paginate(10);

        // Calculate total quantity in grams
        $totalGrams = $staffs->sum('total_grams');
        $totalKg = $totalGrams / 1000;  // Convert grams to kilograms

        // Fetch users for dropdown (if needed)
        $users = Users::all();

        // Pass staff data, users, total kilograms, and search query to the view
        return view('staff_reports.index', compact('staffs', 'users', 'searchQuery', 'totalKg'));
    }
}
