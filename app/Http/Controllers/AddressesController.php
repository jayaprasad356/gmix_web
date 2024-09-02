<?php
namespace App\Http\Controllers;

use App\Http\Requests\AddressesStoreRequest;
use App\Models\Addresses;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AddressesController extends Controller
{

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Addresses::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%")
                  ->orWhere('alternate_mobile', 'like', "%$search%")
                  ->orWhere('door_no', 'like', "%$search%")
                  ->orWhere('street_name', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%")
                  ->orWhere('pincode', 'like', "%$search%")
                  ->orWhere('state', 'like', "%$search%")
                  ->orWhere('landmark', 'like', "%$search%")
                  ->orWhereHas('users', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        if ($request->wantsJson()) {
            return response($query->get());
        }
        $addresses = $query->latest()->paginate(10);
        $users = Users::all();
     
         return view('addresses.index', compact('addresses', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function create()
    {
        return view('addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressesStoreRequest $request)
    {
        $existingAddress = Addresses::where('door_no', $request->door_no)
        ->where('street_name', $request->street_name)
        ->where('landmark', $request->landmark)
        ->first();

    if ($existingAddress) {
        return redirect()->back()->withErrors([
            'address' => 'The address with the same Door No, Street Name, and Landmark already exists.'
        ])->withInput();
    }

    // Custom validation to ensure mobile and alternate_mobile are different
    if ($request->mobile === $request->alternate_mobile) {
        return redirect()->back()->withErrors([
            'mobile' => 'Mobile and Alternate Mobile must be different.',
        ])->withInput();
    }

        $addresses = Addresses::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'door_no' => $request->door_no,
            'street_name' => $request->street_name,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'state' => $request->state,
            'landmark' => $request->landmark,
        ]);

        if (!$addresses) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating addresses.');
        }
        return redirect()->route('addresses.index')->with('success', 'Success, New addresses has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\addresses  $addresses
     * @return \Illuminate\Http\Response
     */
    public function show(Addresses $addresses)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Address  $addresses
     * @return \Illuminate\Http\Response
     */
    public function edit(Addresses $addresses)
    {
        $users = Users::all(); // Fetch all shops
        return view('addresses.edit', compact('addresses', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\addresses  $addresses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Addresses $addresses)
    {
        // Validate that mobile and alternate_mobile are different
        $request->validate([
            'mobile' => 'required|different:alternate_mobile',
            'alternate_mobile' => 'required',
        ]);
    
        // Check if the door_no, street_name, and landmark already exist
        $existingAddress = Addresses::where('door_no', $request->door_no)
                                    ->where('street_name', $request->street_name)
                                    ->where('landmark', $request->landmark)
                                    ->first();
    
        if ($existingAddress && $existingAddress->id != $addresses->id) {
            return redirect()->back()->with('error', 'The address with the same door number, street name, and landmark already exists.');
        }
    
        // Update the address fields
        $addresses->first_name = $request->first_name;
        $addresses->last_name = $request->last_name;
        $addresses->mobile = $request->mobile;
        $addresses->alternate_mobile = $request->alternate_mobile;
        $addresses->door_no = $request->door_no;
        $addresses->street_name = $request->street_name;
        $addresses->city = $request->city;
        $addresses->pincode = $request->pincode;
        $addresses->state = $request->state;
        $addresses->landmark = $request->landmark;
    
        if (!$addresses->save()) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while updating the addresses.');
        }
    
        return redirect()->route('addresses.edit', $addresses->id)->with('success', 'Success, address has been updated.');
    }
    
    public function destroy(Addresses $addresses)
    {
        $addresses->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
