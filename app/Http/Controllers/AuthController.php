<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users; 
use App\Models\Products; 
use App\Models\Addresses; 
use App\Models\Orders;
use App\Models\Friends; 
use App\Models\Points; 
use App\Models\Plans;
use App\Models\Notifications; 
use App\Models\Verifications; 
use App\Models\Transaction; 
use App\Models\Feedback;
use App\Models\Fakechats; 
use App\Models\Professions; 
use App\Models\RechargeTrans;
use App\Models\VerificationTrans;
use App\Models\Appsettings; 
use App\Models\News; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Berkayk\OneSignal\OneSignalClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class AuthController extends Controller
{
 
    public function login(Request $request)
    {
        // Retrieve phone number from the request
        $mobile = $request->input('mobile');

        if (empty($mobile)) {
            $response['success'] = false;
            $response['message'] = 'mobile is empty.';
            return response()->json($response, 400);
        }

        // Remove non-numeric characters from the phone number
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        // Check if the length of the phone number is not equal to 10
        if (strlen($mobile) !== 10) {
            $response['success'] = false;
            $response['message'] = "mobile number should be exactly 10 digits";
            return response()->json($response, 400);
        }


        // Check if a customer with the given phone number exists in the database
        $user = Users::where('mobile', $mobile)->first();

        // If customer not found, register the user
        if (!$user) {
            $user = new Users();
            $user->mobile = $mobile;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'registered' => true,
            'message' => 'Logged in successfully.',
            'data' => [
            'id' => $user->id,
            'name' => $user->name ?? '',
            'mobile' => $user->mobile,
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
            ],
        ], 200);
        }

    public function otp(Request $request)
    {
        // Retrieve phone number from the request
        $mobile = $request->input('mobile');
    
        if (empty($mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number is empty.',
            ], 400);
        }
    
        // Remove non-numeric characters from the phone number
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
        // Check if the length of the phone number is not exactly 10
        if (strlen($mobile) !== 10) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number should be exactly 10 digits.',
            ], 400);
        }
    
        // Generate a random 6-digit OTP
        $randomNumber = mt_rand(100000, 999999);
        $datetime = now();
    
        // Check if the mobile number already exists in the otp table
        $otpRecord = DB::table('otp')->where('mobile', $mobile)->first();
    
        if ($otpRecord) {
            // If exists, update the OTP and datetime
            DB::table('otp')->where('mobile', $mobile)->update([
                'otp' => $randomNumber,
                'datetime' => $datetime,
            ]);
        } else {
            // If not exists, insert a new record
            DB::table('otp')->insert([
                'mobile' => $mobile,
                'otp' => $randomNumber,
                'datetime' => $datetime,
            ]);
        }
    
        // Fetch the updated or newly inserted record
        $otpRecord = DB::table('otp')->where('mobile', $mobile)->first();
    
        return response()->json([
            'success' => true,
            'message' => 'OTP received successfully.',
            'data' => $otpRecord,
        ], 200);
    }
    public function userdetails(Request $request)
    {
        $user_id = $request->input('user_id');

        // Retrieve the user details for the given user_id
        $user = Users::find($user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $userDetails = [[
            'id' => $user->id,
            'name' => $user->name,
            'mobile' => $user->mobile,
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ]];

        return response()->json([
            'success' => true,
            'message' => 'User details retrieved successfully.',
            'data' => $userDetails,
        ], 200);
    }
    public function product_list(Request $request)
    {
        $products = Products::orderBy('price', 'desc')->get();

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products found.',
            ], 404);
        }

        $productsDetails = [];


        foreach ($products as $product) {

            $imageUrl = $product->image ? asset('storage/app/public/products/' . $product->image) : '';
            $productsDetails[] = [
                'id' => $product->id,
                'name' => $product->name,
                'unit' => $product->unit,
                'measurement' => $product->measurement,
                'price' => (string) $product->price,
                'image' => $imageUrl,
                'updated_at' => Carbon::parse($product->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($product->created_at)->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'products Details retrieved successfully.',
            'data' => $productsDetails,
        ], 200);
    }
    public function add_address(Request $request)
    {
        $user_id = $request->input('user_id'); 
        $name = $request->input('name');
        $mobile = $request->input('mobile');
        $alternate_mobile = $request->input('alternate_mobile');
        $door_no = $request->input('door_no');
        $street_name = $request->input('street_name');
        $city = $request->input('city');
        $pincode = $request->input('pincode');
        $state = $request->input('state');
        $landmark = $request->input('landmark');

        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 400);
        }

        if (empty($name)) {
            return response()->json([
                'success' => false,
                'message' => 'name is empty.',
            ], 400);
        }

        if (empty($mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'mobile is empty.',
            ], 400);
        }

        if (empty($alternate_mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'Alternate Mobile is empty.',
            ], 400);
        }

        if (empty($door_no)) {
            return response()->json([
                'success' => false,
                'message' => 'door_no is empty.',
            ], 400);
        }

        if (empty($street_name)) {
            return response()->json([
                'success' => false,
                'message' => 'street_name is empty.',
            ], 400);
        }

        if (empty($city)) {
            return response()->json([
                'success' => false,
                'message' => 'city is empty.',
            ], 400);
        }

        if (empty($pincode)) {
            return response()->json([
                'success' => false,
                'message' => 'pincode is empty.',
            ], 400);
        }

        if (empty($state)) {
            return response()->json([
                'success' => false,
                'message' => 'state is empty.',
            ], 400);
        }

        // Check if user exists
        $user = Users::find($user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'user not found.',
            ], 404);
        }

        // Create a new Address instance
        $address = new Addresses();
        $address->user_id = $user_id; 
        $address->name = $name;
        $address->mobile = $mobile;
        $address->alternate_mobile = $alternate_mobile;
        $address->door_no = $door_no;
        $address->street_name = $street_name;
        $address->city = $city;
        $address->pincode = $pincode;
        $address->state = $state;
        $address->landmark = $landmark;

        // Save the address
        if (!$address->save()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save address.',
            ], 500);
        }

        $addressDetails = [[
            'id' => $address->id,
            'user_id' => $address->user_id,
            'name' => $address->name,
            'mobile' => $address->mobile,
            'alternate_mobile' => $address->alternate_mobile,
            'door_no' => $address->door_no,
            'street_name' => $address->street_name,
            'city' => $address->city,
            'pincode' => $address->pincode,
            'state' => $address->state,
            'landmark' => $address->landmark ?? '',
            'updated_at' => Carbon::parse($address->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($address->created_at)->format('Y-m-d H:i:s'),
        ]];

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
            'data' => $addressDetails,
        ], 201);
    }

     public function address_list(Request $request)
    {
        $address_id = $request->input('address_id');

        if (empty($address_id)) {
            return response()->json([
                'success' => false,
                'message' => 'address_id is empty.',
            ], 400);
        }


        // Retrieve the user details for the given user_id
        $address = addresses::find($address_id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'address not found.',
            ], 404);
        }

        $addressDetails = [[
            'id' => $address->id,
            'user_id' => $address->user_id,
            'name' => $address->name,
            'mobile' => $address->mobile,
            'alternate_mobile' => $address->alternate_mobile,
            'door_no' => $address->door_no,
            'street_name' => $address->street_name,
            'city' => $address->city,
            'pincode' => $address->pincode,
            'state' => $address->state,
             'landmark' => $address->landmark ?? '',
            'updated_at' => Carbon::parse($address->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($address->created_at)->format('Y-m-d H:i:s'),
        ]];

        return response()->json([
            'success' => true,
            'message' => 'Address details retrieved successfully.',
            'data' => $addressDetails,
        ], 200);
    }

    public function place_order(Request $request)
    {
        $user_id = $request->input('user_id'); 
        $product_id = $request->input('product_id');
        $address_id = $request->input('address_id');
        $price = $request->input('price');
        $delivery_charges = $request->input('delivery_charges');
        $payment_mode = $request->input('payment_mode');
    
        // Validate inputs
        if (empty($user_id) || empty($product_id) || empty($address_id) || empty($price) || empty($delivery_charges) || empty($payment_mode)) {
            return response()->json([
                'success' => false,
                'message' => 'One or more required fields are empty.',
            ], 400);
        }
    
        // Check if user exists
        $user = Users::find($user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
    
        // Check if product exists
        $product = Products::find($product_id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }
    
        // Check if address exists
        $address = Addresses::find($address_id);
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }
    
        // Check if the order already exists
        $existingOrder = Orders::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->where('address_id', $address_id)
            ->first();
    
        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Order already exists.',
            ], 400);
        }
    
        // Check if payment mode is valid
        if ($payment_mode !== 'prepaid' && $payment_mode !== 'cod') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment mode. Payment mode should be either prepaid or cod.',
            ], 400);
        }
    
        // Insert into orders table
        $order = new Orders();
        $order->user_id = $user_id;
        $order->product_id = $product_id;
        $order->address_id = $address_id;
        $order->price = $price;
        $order->delivery_charges = $delivery_charges;
        $order->payment_mode = $payment_mode;
        $order->save();
    
        // Prepare data for Shiprocket order creation
        $orderData = [
            "order_id" => $order->id,
            "order_date" => now()->format('Y-m-d H:i'),
            "pickup_location" => "Trichy", // Assuming a static pickup location, change as needed
            "channel_id" => "", // If required, add channel_id logic here
            "comment" => "Reseller: M/s Goku", // Optional comment
            "billing_customer_name" => $user->name,
            "billing_last_name" => $user->last_name,
            "billing_address" => $address->address_line_1,
            "billing_address_2" => $address->address_line_2,
            "billing_city" => $address->city,
            "billing_pincode" => $address->pincode,
            "billing_state" => $address->state,
            "billing_country" => "India",
            "billing_email" => $user->email,
            "billing_phone" => $user->mobile,
            "shipping_is_billing" => true,
            "order_items" => [
                [
                    "name" => $product->name,
                    "sku" => $product->sku,
                    "units" => 1, // Assuming 1 unit, adjust as necessary
                    "selling_price" => $price,
                    "discount" => "", // Add discount if applicable
                    "tax" => "", // Add tax if applicable
                    "hsn" => $product->hsn_code, // Assuming product has HSN code
                ]
            ],
            "payment_method" => $payment_mode === 'prepaid' ? 'Prepaid' : 'COD',
            "shipping_charges" => $delivery_charges,
            "giftwrap_charges" => 0,
            "transaction_charges" => 0,
            "total_discount" => 0,
            "sub_total" => $price,
            "length" => $product->length, // Assuming product has dimensions
            "breadth" => $product->breadth,
            "height" => $product->height,
            "weight" => $product->weight,
        ];
    
        // Call the createOrder method
        $response = $this->createOrder($orderData);
    
        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'Order placed and Shiprocket order created successfully.',
                'data' => $response->json(),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Order placed, but failed to create Shiprocket order.',
                'error' => $response->json(),
            ], $response->status());
        }
    }
    
    // Modified createOrder function to accept order data
    public function createOrder($orderData)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjUwOTY4OTAsInNvdXJjZSI6InNyLWF1dGgtaW50IiwiZXhwIjoxNzI1OTIwMjcyLCJqdGkiOiJ6VFFtdjV4RWRrNE1IbTdLIiwiaWF0IjoxNzI1MDU2MjcyLCJpc3MiOiJodHRwczovL3NyLWF1dGguc2hpcHJvY2tldC5pbi9hdXRob3JpemUvdXNlciIsIm5iZiI6MTcyNTA1NjI3MiwiY2lkIjoyNzI4MzUyLCJ0YyI6MzYwLCJ2ZXJib3NlIjpmYWxzZSwidmVuZG9yX2lkIjowLCJ2ZW5kb3JfY29kZSI6IiJ9.sLpaoPK_vihXBiFO6ivYzXk6WX9-iORL28RYzz8UPxY'
        ])->post('https://apiv2.shiprocket.in/v1/external/orders/create/adhoc', $orderData);
    
        return $response;
    }
    
    public function orders_list(Request $request)
    {
        $user_id = $request->input('user_id');

        // Retrieve the orders for the given user
        $orders = Orders::where('user_id', $user_id)->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No orders found for the user.',
            ], 404);
        }

        $ordersDetails = [];

        foreach ($orders as $order) {

            if ($order->user_id == $user_id) {
                $user = Users::find($order->user_id);
                $product = Products::find($order->product_id);
                $address = Addresses::find($order->address_id);
                $ordersDetails[] = [
                    'id' => $order->id,
                    'user_name' => $user->name, // Retrieve user name from the User model
                    'address_name' => $address->name, // Retrieve address name from the Address model
                    'product_name' => $product->name, // Retrieve product name from the Product model
                    'delivery_charges' => $order->delivery_charges,
                    'payment_mode' => $order->payment_mode,
                    'price' => (string) $order->price,
                    'place_status' => (string) $order->place_status,
                    'delivery_date' => Carbon::parse($order->delivery_date)->format('Y-m-d'),
                    'updated_at' => Carbon::parse($order->updated_at)->format('Y-m-d H:i:s'),
                    'created_at' => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully.',
            'data' => $ordersDetails,
        ], 200);
    }

    public function my_address_list(Request $request)
    {
        $user_id = $request->input('user_id');

        // Retrieve the orders for the given user
        $addresses = Addresses::where('user_id', $user_id)->get();

        if ($addresses->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No addresses found for the user.',
            ], 404);
        }

        $addressesDetails = [];

        foreach ($addresses as $address) {

            if ($address->user_id == $user_id) {
                $user = Users::find($address->user_id);
                $addressesDetails[] = [
                    'id' => $address->id,
                    'user_id' => $address->user_id,
                    'user_name' => $user->name,
                    'name' => $address->name,
                    'mobile' => $address->mobile,
                    'alternate_mobile' => $address->alternate_mobile,
                    'door_no' => $address->door_no,
                    'street_name' => $address->street_name,
                    'city' => $address->city,
                    'pincode' => $address->pincode,
                    'state' => $address->state,
                     'landmark' => $address->landmark ?? '',
                    'updated_at' => Carbon::parse($address->updated_at)->format('Y-m-d H:i:s'),
                    'created_at' => Carbon::parse($address->created_at)->format('Y-m-d H:i:s'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Addresses retrieved successfully.',
            'data' => $addressesDetails,
        ], 200);
    }

    public function settings_list(Request $request)
{
    // Retrieve all news settings
    $news = News::all();

    if ($news->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No settings found.',
        ], 404);
    }

    $newsData = [];
    foreach ($news as $item) {
        $newsData[] = [
            'id' => $item->id,
            'delivery_charges' => $item->delivery_charges,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Settings listed successfully.',
        'data' => $newsData,
    ], 200);
}

}