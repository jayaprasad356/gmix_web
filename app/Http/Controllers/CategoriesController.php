<?php

namespace App\Http\Controllers;

use App\Http\Requests\categoriesStoreRequest;
use App\Models\categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
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
                categories::all()
            );
        }
        $categories = categories::latest()->paginate(10);
        return view('categories.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(categoriesStoreRequest $request)
    {
        $imagePath = $request->file('image')->store('categories', 'public');

        $categories = categories::create([
            'image' => basename($imagePath),
            'name' => $request->name,
        ]);

        if (!$categories) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating shop.');
        }
        return redirect()->route('categories.index')->with('success', 'Success, New Categories has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shops  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(categories $categories)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slides  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(categories $categories)
    {
        return view('categories.edit', compact('categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shops  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, categories $categories)
    {
        $categories->name = $request->name;

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('categories', 'public');
            // Delete old image if it exists
            Storage::disk('public')->delete('categories/' . $categories->image);
            $categories->image = basename($newImagePath);
        }

        if (!$categories->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('categories.index')->with('success', 'Success, The Categories has been updated.');
    }

    public function destroy(categories $categories)
    {
        if (Storage::disk('public')->exists('categories/' . $categories->image)) {
            Storage::disk('public')->delete('categories/' . $categories->image);
        }
        $categories->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
