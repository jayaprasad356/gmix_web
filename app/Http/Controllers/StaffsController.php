<?php
namespace App\Http\Controllers;

use App\Models\Staffs;
use App\Models\StaffTransactions;
use Illuminate\Http\Request;
use App\Http\Requests\StaffsStoreRequest;


class StaffsController extends Controller
{
    public function index(Request $request)
    {
        $query = Staffs::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%");
        }

        $staffs = $query->latest()->paginate(10);

        return view('staffs.index', compact('staffs'));
    }

    public function create()
    {
        return view('staffs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(StaffsStoreRequest $request)
    {

        $staffs = Staffs::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'password' => $request->password,
        ]);

        if (!$staffs) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating profession.');
        }
        return redirect()->route('staffs.index')->with('success', 'Success, New Staff has been added successfully!');
    }

    public function edit(Staffs $staffs)
    {
        return view('staffs.edit', compact('staffs'));
    }

    public function update(Request $request, Staffs $staffs)
    {
        $staffs->update($request->only(['name', 'mobile', 'password']));

        return redirect()->route('staffs.edit', $staffs->id)->with('success', 'Staff has been updated.');
    }

    public function addIncentivesForm($id)
    {
        $staff = Staffs::find($id);

        if (!$staff) {
            return redirect()->route('staffs.index')->with('error', 'Staff not found.');
        }

        return view('staffs.add_incentives', compact('staff'));
    }

    public function addIncentives(Request $request, $id)
    {
        $request->validate([
            'incentives' => 'required|integer',
        ]);

        $staff = Staffs::find($id);

        if (!$staff) {
            return redirect()->route('staffs.index')->with('error', 'Staff not found.');
        }

        // Update staff incentives
        $staff->incentives += $request->input('incentives');
        $staff->total_incentives += $request->input('incentives');
        $staff->save();

        // Create a new transaction record
        StaffTransactions::create([
            'staff_id' => $staff->id,
            'type' => 'incentives',
            'amount' => $request->input('incentives'),
            'datetime' => now(),
        ]);

        return redirect()->route('staffs.index')->with('success', 'Incentives added successfully.');
    }

    public function destroy(Staffs $staffs)
    {
        $staffs->delete();

        return response()->json(['success' => true]);
    }
}
