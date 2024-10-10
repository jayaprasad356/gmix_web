<?php

namespace App\Http\Controllers;

use App\Http\Requests\image_slidersStoreRequest;
use App\Models\image_sliders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageSlidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response(
                image_sliders::all()
            );
        }
        $image_sliders = image_sliders::latest()->paginate(10);
        return view('image_sliders.index')->with('image_sliders', $image_sliders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('image_sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(image_slidersStoreRequest $request)
    {
        $imagePath = $request->file('image')->store('image_sliders', 'public');

        $image_sliders = image_sliders::create([
            'image' => basename($imagePath),
            'name' => $request->name,
            'link' => $request->link,
        ]);

        if (!$image_sliders) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating shop.');
        }
        return redirect()->route('image_sliders.index')->with('success', 'Success, New Image Slides has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shops  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(image_sliders $image_sliders)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slides  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(image_sliders $image_sliders)
    {
        return view('image_sliders.edit', compact('image_sliders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shops  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, image_sliders $image_sliders)
    {
        $image_sliders->name = $request->name;
        $image_sliders->link = $request->link;

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('image_sliders', 'public');
            // Delete old image if it exists
            Storage::disk('public')->delete('image_sliders/' . $image_sliders->image);
            $image_sliders->image = basename($newImagePath);
        }

        if (!$image_sliders->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('image_sliders.index')->with('success', 'Success, The Image Sliders has been updated.');
    }

    public function destroy(image_sliders $image_sliders)
    {
        if (Storage::disk('public')->exists('image_sliders/' . $image_sliders->image)) {
            Storage::disk('public')->delete('image_sliders/' . $image_sliders->image);
        }
        $image_sliders->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
