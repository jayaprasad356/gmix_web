<?php

namespace App\Http\Controllers;

use App\Http\Requests\RewardPointsStoreRequest;
use App\Models\Reward_points;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardpointsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Reward_points::query();
        
        // Check if there's a search query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('points', 'like', "%$search%");
            $query->where('name', 'like', "%$search%");
        }
        
        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response($query->get());
        }
        
        // Retrieve all points if there's no search query
        $reward_points = $query->latest()->paginate(10);
        
        return view('reward_points.index')->with('reward_points', $reward_points);
    }
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('reward_points.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    
    public function store(RewardPointsStoreRequest $request)
    {
        $reward_points = Reward_points::create([
            'points' => $request->points,
            'name' => $request->name,
            'description' => $request->description,
        ]);
    
        if (!$reward_points) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating user.');
        }
    
        return redirect()->route('reward_points.index')->with('success', 'Success, New Reward Points has been added successfully!');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\points  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(reward_points $reward_points)
    {

    }

    public function reward_points()
{
    return $this->belongsTo(Reward_points::class);
}
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Points $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Reward_points $reward_points)
    {
        return view('reward_points.edit', compact('reward_points'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Points  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reward_points $reward_points)

    {
        $reward_points->points = $request->points;
        $reward_points->name = $request->name;
        $reward_points->description = $request->description;
        

        if (!$reward_points->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('reward_points.edit', $reward_points->id)->with('success', 'Success, Reward Points has been updated.');
    }

    public function destroy(Reward_points $reward_points)
    {
        $reward_points->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
