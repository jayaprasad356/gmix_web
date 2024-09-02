<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Addresses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\OrderStoreRequest;

class OrdersController extends Controller
{

    public function verify(Request $request)
{
    $orderIds = $request->input('order_ids', []);
    $newStatus = $request->input('status'); // Get the status from the request

    foreach ($orderIds as $orderId) {
        $order = Orders::find($orderId);
        if ($order) {
            // Update the order status to the selected status
            $order->status = $newStatus;
            $order->save();
        }
    }

    return response()->json(['success' => true]);
}

    
    
    public function index(Request $request)
{
    $query = Orders::query()->with('user', 'addresses', 'product'); // Eager load relationships

    // Search functionality
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('addresses', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('price', 'like', "%{$search}%")
              ->orWhere('delivery_charges', 'like', "%{$search}%")
              ->orWhere('payment_mode', 'like', "%{$search}%");
        });
    }

    // Status filter functionality
    if ($request->filled('status')) {
        $status = $request->input('status');
        $query->where('status', $status);
    }

    // Default sorting: Show pending orders first, then others, sorted by latest date
    $query->orderByRaw('CASE WHEN status = 0 THEN 0 ELSE 1 END, created_at DESC');

    // Paginate the results
    $orders = $query->paginate(10);

    // Check if the request is AJAX
    if ($request->wantsJson()) {
        return response()->json($orders);
    }

    $users = Users::all(); // Fetch all users for the filter dropdown
    $products = Products::all(); // Fetch all products for the filter dropdown

    return view('orders.index', compact('users', 'orders', 'products'));
}

    public function create()
{
    $products = Products::all();
    $users = Users::all();
    $addresses = Addresses::all();
    
    return view('orders.create', compact('products', 'users','addresses'));
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    
    public function store(OrderStoreRequest $request)
    {
        $orders = Orders::create([
            'user_id' => $request->user_id,
            'address_id' => $request->address_id,
            'product_id' => $request->product_id,
            'price' => $request->price,
            'delivery_charges' => $request->delivery_charges,
            'payment_mode' => $request->payment_mode,
        ]);
    
        if (!$orders) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating user.');
        }
    
        return redirect()->route('orders.index')->with('success', 'Success, New orders has been added successfully!');
    }
    public function getUserAddresses($userId)
    {
        // Fetch addresses associated with the user
        $addresses = Addresses::where('user_id', $userId)->get(['id', 'name']);
    
        // Return the addresses as a JSON response
        return response()->json($addresses);
    }
    
}
