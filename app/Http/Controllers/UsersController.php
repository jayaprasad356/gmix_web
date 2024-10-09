<?php
namespace App\Http\Controllers;

use App\Http\Requests\UsersStoreRequest;
use App\Models\Users;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = Users::query(); // Removed 'with' if not necessary

        // If search is provided
        if ($request->has('search')) {
            $search = $request->input('search');
            // Search by mobile only
            $query->where('mobile', 'like', "%$search%");
        }

        // Get perPage value from the request, default to 10
        $perPage = $request->input('perPage', 10);
        
        // Pagination
        $users = $query->latest()->paginate($perPage); // Show latest users first

        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('users.index', compact('users', 'perPage'));
    }

    public function store(UsersStoreRequest $request)
    {
        $user = Users::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            // No staff_id field
        ]);

        if (!$user) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while creating the user.');
        }
        
        return redirect()->route('users.index')->with('success', 'Success! New user has been added successfully!');
    }

    public function edit(Users $users)
    {
        // No need to load staff data if not needed
        return view('users.edit', compact('users'));
    }

    public function update(Request $request, Users $user)
    {
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->points = $request->points;
        $user->total_points = $request->total_points;
        // No staff_id field

        if (!$user->save()) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while updating the user.');
        }
        
        return redirect()->route('users.edit', $user->id)->with('success', 'Success! User has been updated.');
    }

    public function destroy(Users $user)
    {
        $user->delete();

        return response()->json(['success' => true]);
    }
}
