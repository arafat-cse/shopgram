<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = auth()->user()->reviews()->with('product')->paginate(10);
        return view('customer.reviews.index', compact('reviews'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|between:1,5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->product_id],
            [
                'rating'   => $request->rating,
                'comment'  => $request->comment,
                'status'   => 'pending',
                'order_id' => $request->order_id,
            ]
        );

        return back()->with('success', 'Review submitted for approval.');
    }
}
