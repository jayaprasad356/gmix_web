<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function edit()
    {
        // Assuming you are editing the News with ID 1
        $news = News::findOrFail(1);
        return view('news.edit', compact('news'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'delivery_charges' => 'required|string|max:255',
            'customer_support_number' => 'required|string|max:255',
            'upi_id' => 'required|string|max:255',
            'privacy_policy' => 'required|string',
            'terms_conditions' => 'required|string',
            'refund_policy' => 'required|string',
        ]);

        $news = News::findOrFail(1); // Again, assuming ID 1 for simplicity
        $news->delivery_charges = $request->input('delivery_charges');
        $news->customer_support_number = $request->input('customer_support_number');
        $news->upi_id = $request->input('upi_id');
        $news->privacy_policy = $request->input('privacy_policy');
        $news->terms_conditions = $request->input('terms_conditions');
        $news->refund_policy = $request->input('refund_policy');
    

        if ($news->save()) {
            return redirect()->route('news.edit')->with('success', 'Success, Settings has been updated.');
        } else {
            return redirect()->route('news.edit')->with('error', 'Sorry, something went wrong while updating the News.');
        }
    }
}



