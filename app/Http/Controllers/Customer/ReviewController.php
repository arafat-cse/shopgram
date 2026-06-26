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

        $productId = $request->product_id;
        $deliveredOrder = \App\Models\Order::where('user_id', auth()->id())
            ->where('status', 'delivered')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->first();

        if (!$deliveredOrder) {
            return back()->withErrors(['product_id' => 'You can only review products you have purchased and that have been delivered.']);
        }

        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $productId],
            [
                'rating'   => $request->rating,
                'comment'  => $request->comment,
                'status'   => 'pending',
                'order_id' => $deliveredOrder->id,
            ]
        );

        return back()->with('success', 'Review submitted for approval.');
    }
}
