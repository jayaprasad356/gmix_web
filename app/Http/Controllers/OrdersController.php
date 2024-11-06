<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Addresses;
use App\Models\Staffs;
use App\Models\StaffTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\OrderStoreRequest;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

                  // Check if the status is '2' (for deduction)
            if ($newStatus == 2) {
                // Fetch the product's incentive value
                $product = Products::find($order->product_id);
                if ($product && $product->incentives) {
                    $incentives = $product->incentives;

                    // Insert a transaction with negative incentive into staff_transactions table
                    StaffTransactions::create([
                        'staff_id' => $order->staff_id,
                        'amount' => -$incentives, // Deduct the incentive
                        'type' => 'cancelled',
                        'datetime' => now(),
                        'updated_at' => now(),
                    ]);
                    $staff = Staffs::find($order->staff_id);
                    if ($staff) {
                        $staff->incentives -= $incentives; // Deduct the incentive
                        $staff->total_incentives -= $incentives; // Deduct from total incentives as well
                        $staff->save(); // Save the updated values
                    }
                }
            }
    
                // Execute Shiprocket API request only if the status is 'Confirmed' (1) and ship_rocket is not already 1
                if ($newStatus == 1 && $order->ship_rocket != 1) {
                    // Fetch necessary data
                    $address = Addresses::find($order->address_id);
                    $product = Products::find($order->product_id);
                    $price = $order->price;
                    $delivery_charges = $order->delivery_charges;
                    $total_price = $order->price;
                    $address1 = $address->door_no . ' ' . $address->street_name;
                    $payment_mode = $order->payment_mode;
    
                     // Shiprocket API request
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getShiprocketToken(), // Use the method to get the token
                ])->post('https://apiv2.shiprocket.in/v1/external/orders/create/adhoc', [
                    "order_id" => "Gmix-" . $orderId, // Use current order ID with prefix "Gmix-"
                    "order_date" => Carbon::now()->format('Y-m-d H:i:s'),
                        "pickup_location" => "Trichy",
                        "channel_id" => "",
                        "comment" => "G Mix",
                        "billing_customer_name" => $address->first_name,
                        "billing_last_name" => $address->last_name,
                        "billing_address" => $address1,
                        "billing_address_2" => "",
                        "billing_city" => $address->city,
                        "billing_pincode" => $address->pincode,
                        "billing_state" => $address->state,
                        "billing_country" => "India",
                        "billing_email" => "",
                        "billing_phone" => $address->mobile,
                        "shipping_is_billing" => true,
                        "order_items" => [
                            [
                                "name" => $product->name,
                                "sku" => "123456",
                                "units" => 1,
                                "selling_price" => $price,
                                "discount" => "",
                                "tax" => "",
                                "hsn" => 441122
                            ]
                        ],
                        "payment_method" => $payment_mode,
                        "shipping_charges" => (int) $delivery_charges,
                        "giftwrap_charges" => 0,
                        "transaction_charges" => 0,
                        "total_discount" => 0,
                        "sub_total" => (int) $total_price,
                        "length" => 8,
                        "breadth" => 4,
                        "height" => 5,
                        "weight" => 0.5
                    ]);
    
                    if ($response->successful()) {
                        // If the API request is successful, update the ship_rocket status
                        $order->ship_rocket = 1;
                        $order->save();
                    } else {
                        // If the API request fails, return an error response
                        // Decode the JSON response body to extract the errors
                        $responseBody = $response->json(); // Get the JSON response as an array
                        $errors = isset($responseBody['errors']) ? $responseBody['errors'] : 'No errors found';
                    
                        // Return only the errors part of the response
                        return response()->json([
                            'success' => false, 
                            'errors' => $errors // Return only the errors from the response
                        ], 500);
                    }
                } else {
                    // If status is not "Confirmed" or ship_rocket is already 1, just save the order status
                    $order->save();
                }
            }
        }
    
        return response()->json(['success' => true]);
    }
       // Private method for fetching the Shiprocket API token
       private function getShiprocketToken()
       {
           $response = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login', [
               'email' => 'gmix7418@gmail.com',
               'password' => '$Jap0111',
           ]);
   
           if ($response->successful()) {
               return $response->json()['token']; // Return the token from the response
           }
   
           throw new \Exception('Shiprocket Authentication Failed');
       }
   
    public function index(Request $request)
    {
        $query = Orders::query()->with(['user','staffs', 'addresses', 'product']); // Eager load relationships
    
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
    
        // Status filter functionality (if required)
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }
    
        // Default sorting: Show pending orders first, then others, sorted by latest date
        $query->orderByRaw('CASE WHEN status = 0 THEN 0 ELSE 1 END');
        $query->orderBy('created_at', 'desc');
    
        // Paginate the results
        $orders = $query->paginate(10);
    
        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response()->json($orders);
        }
    
        $users = Users::all();  // Fetch all users for the filter dropdown
        $products = Products::all();  // Fetch all products for the filter dropdown
        $staffs = Staffs::all();  // Fetch all staffs for the filter dropdown
    
        return view('orders.index', compact('users', 'orders', 'products', 'staffs'));
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
    public function edit(Orders $order)
    {
        // Fetch the address associated with the order
        $addresses = Addresses::find($order->address_id);
    
        if (!$addresses) {
            return redirect()->route('orders.index')->with('error', 'Address not found.');
        }
    
        return view('orders.edit', compact('order', 'addresses'));
    }
    
    
    public function update(Request $request, Orders $order)
{
    // Validate the input
    $rules = [
        'address_id' => 'required|exists:addresses,id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'mobile' => 'required|numeric',
        'alternate_mobile' => 'required|numeric',
        'door_no' => 'required|string|max:255',
        'street_name' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'pincode' => 'required|numeric',
        'state' => 'required|string|max:255',
        'landmark' => 'nullable|string|max:255',
        'chat_conversation' => 'nullable|image', // Mandatory for both payment modes
    ];

    // Conditionally validate payment_image only if payment_mode is Prepaid
    if ($request->input('payment_mode') === 'Prepaid') {
        $rules['payment_image'] = 'required|image';  // Make it required if prepaid
    }

    $request->validate($rules);

    // Update the Address
    $addresses = Addresses::find($request->input('address_id'));
    if (!$addresses) {
        return redirect()->route('orders.index')->with('error', 'Address not found.');
    }

    $addresses->update([
        'door_no' => $request->input('door_no'),
        'street_name' => $request->input('street_name'),
        'city' => $request->input('city'),
        'pincode' => $request->input('pincode'),
        'state' => $request->input('state'),
        'landmark' => $request->input('landmark'),
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'mobile' => $request->input('mobile'),
        'alternate_mobile' => $request->input('alternate_mobile'),
    ]);

    // Fetch product price based on product_id
    $product = Products::find($order->product_id);
    if (!$product) {
        return redirect()->route('orders.index')->with('error', 'Product not found!');
    }

    $price = $product->price;
    $delivery_charges = 0;
    $total_price = 0;
    $status = $order->status;  // Keep current status unless overridden

    // Condition based on payment mode
    if ($request->input('payment_mode') === 'Prepaid') {
        // Prepaid orders: No delivery charges, status is default (0)
        $total_price = $price;
        $status = 0; // Default status for prepaid orders
    } else if ($request->input('payment_mode') === 'COD') {
        $charges_result = DB::table('news')->orderBy('id', 'desc')->value('delivery_charges');

        if (!is_null($charges_result)) {
            $delivery_charges = $charges_result;
        }

        $total_price = $price + $delivery_charges;
        $status = 5; // COD orders have status set to 5
    }

    // Handle chat_conversation image upload
    if ($request->hasFile('chat_conversation')) {
        $newImagePath = $request->file('chat_conversation')->store('orders', 'public');
        Storage::disk('public')->delete('orders/' . $order->chat_conversation);
        $order->chat_conversation = basename($newImagePath);
    }

    // Handle payment_image upload if payment_mode is Prepaid
    if ($request->hasFile('payment_image') && $request->input('payment_mode') === 'Prepaid') {
        $newImagePath = $request->file('payment_image')->store('orders', 'public');
        Storage::disk('public')->delete('orders/' . $order->payment_image);
        $order->payment_image = basename($newImagePath);
    }

    // Update order fields
    $order->price = $price;
    $order->delivery_charges = $delivery_charges;
    $order->total_price = $total_price;
    $order->status = $status;
    $order->payment_mode = $request->input('payment_mode');
    $order->save();

    return redirect()->route('orders.index')->with('success', 'Order and Address updated successfully.');
}

    

    public function getUserAddresses($userId)
    {
        // Fetch addresses associated with the user
        $addresses = Addresses::where('user_id', $userId)->get(['id', 'name']);
    
        // Return the addresses as a JSON response
        return response()->json($addresses);
    }
    
}
