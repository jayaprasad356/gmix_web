<?php

namespace App\Http\Controllers;

use App\Models\Withdrawals;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Staffs;
use App\Models\Users;
use Illuminate\Http\Request;

class VerifyordersController extends Controller
{
    public function verify(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
    
        foreach ($orderIds as $orderId) {
            $order = Orders::find($orderId);
            if ($order) {
                $order->status = 0;
                $order->save();
    
                // Fetch the staff_id from the order
                $staffId = $order->staff_id;
    
                // Get the related product and its incentives
                $product = Products::find($order->product_id);
                if ($product) {
                    // Assuming the product has an 'incentives' field
                    $incentiveAmount = $product->incentives;  // Replace 'incentive' with the actual field name from the Products model
    
                    // Add the incentive to the staff (assuming a Staff model exists)
                    $staff = Staffs::find($staffId);
                    if ($staff) {
                        $staff->incentives += $incentiveAmount;  // Assuming 'incentives' is a field in the Staffs model
                        $staff->total_incentives += $incentiveAmount;  // Assuming 'incentives' is a field in the Staffs model
                        $staff->save();
                    }
                }
            }
        }
    
        return response()->json(['success' => true]);
    }
    

    public function index(Request $request)
    {
        $query = Orders::query()->with(['user', 'staffs', 'addresses', 'product']); // Eager load relationships
        
        // Filter orders by status = 6
        $query->where('status', 6);
    
        // Search functionality (if required)
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('mobile', 'like', "%{$search}%");
                })->orWhereHas('addresses', function($q) use ($search) {
                    $q->where('door_no', 'like', "%{$search}%")
                      ->orWhere('street_name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('pincode', 'like', "%{$search}%")
                      ->orWhere('state', 'like', "%{$search}%")
                      ->orWhere('landmark', 'like', "%{$search}%");
                })->orWhereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('price', 'like', "%{$search}%")
                  ->orWhere('delivery_charges', 'like', "%{$search}%")
                  ->orWhere('payment_mode', 'like', "%{$search}%");
            });
        }
        
        // Paginate the results
        $orders = $query->paginate(10);
    
        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response()->json($orders);
        }
    
        $users = Users::all();  // Fetch all users for the filter dropdown
        $products = Products::all();  // Fetch all products for the filter dropdown
        $staffs = Staffs::all();  // Fetch all staffs for the filter dropdown
    
        return view('verifyorders.index', compact('users', 'orders', 'products', 'staffs'));
    }
    public function destroy(orders $verifyorders)
    {
        $verifyorders->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    
}
