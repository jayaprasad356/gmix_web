<?php
namespace App\Http\Controllers;

use App\Http\Requests\ReviewsStoreRequest;
use App\Models\Reviews;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $query = Reviews::query()->with('products'); // Load product relationship

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('description', 'like', "%$search%");
        }

        if ($request->wantsJson()) {
            return response($query->get());
        }

        $reviews = $query->latest()->paginate(10);
        $products = Products::all(); // Fetch all products for the filter dropdown

        return view('reviews.index', compact('reviews', 'products'));
    }

    public function create()
    {
        $products = Products::all(); // Fetch all products
        return view('reviews.create', compact('products')); // Pass products to the view
    }

    public function store(ReviewsStoreRequest $request)
    {
        // Image handling
        $imageName1 = $request->hasFile('image1') ? $request->file('image1')->store('reviews', 'public') : null;
        $imageName2 = $request->hasFile('image2') ? $request->file('image2')->store('reviews', 'public') : null;
        $imageName3 = $request->hasFile('image3') ? $request->file('image3')->store('reviews', 'public') : null;

        $reviews = Reviews::create([
            'product_id' => $request->product_id,
            'description' => $request->description,
            'ratings' => $request->ratings,
            'image1' => $imageName1 ? basename($imageName1) : null,
            'image2' => $imageName2 ? basename($imageName2) : null,
            'image3' => $imageName3 ? basename($imageName3) : null,
        ]);

        if (!$reviews) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while creating the review.');
        }
        return redirect()->route('reviews.index')->with('success', 'New review added successfully!');
    }

    public function edit(Reviews $review)
    {
        $products = Products::all(); // Fetch all products for the dropdown
        return view('reviews.edit', compact('review', 'products'));
    }

    public function update(Request $request, Reviews $review)
    {
        $review->product_id = $request->product_id;
        $review->ratings = $request->ratings;
        $review->description = $request->description;

        // Image updates
        if ($request->hasFile('image1')) {
            Storage::disk('public')->delete('reviews/' . $review->image1); // Delete old image
            $review->image1 = basename($request->file('image1')->store('reviews', 'public')); // Store new image
        }
        if ($request->hasFile('image2')) {
            Storage::disk('public')->delete('reviews/' . $review->image2);
            $review->image2 = basename($request->file('image2')->store('reviews', 'public'));
        }
        if ($request->hasFile('image3')) {
            Storage::disk('public')->delete('reviews/' . $review->image3);
            $review->image3 = basename($request->file('image3')->store('reviews', 'public'));
        }

        if (!$review->save()) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while updating the review.');
        }

        return redirect()->route('reviews.index')->with('success', 'Review updated successfully.');
    }

    public function destroy(Reviews $reviews)
    {
        if ($reviews->image1) {
            Storage::disk('public')->delete('reviews/' . $reviews->image1);
        }
        if ($reviews->image2) {
            Storage::disk('public')->delete('reviews/' . $reviews->image2);
        }
        if ($reviews->image3) {
            Storage::disk('public')->delete('reviews/' . $reviews->image3);
        }

        $reviews->delete();

        return response()->json(['success' => true]);
    }
}
