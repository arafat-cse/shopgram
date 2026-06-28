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
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|between:1,5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $productId = (int) $request->product_id;
        $orderId   = (int) $request->order_id;
        $userId    = auth()->id();

        // Verify the order belongs to this user, is delivered, and contains the product
        $order = \App\Models\Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('status', 'delivered')
            ->whereHas('items', fn($q) => $q->where('product_id', $productId))
            ->first();

        if (!$order) {
            return back()->withErrors(['product_id' => 'Invalid order or product not purchased.']);
        }

        // Check not already reviewed for this specific order
        if (Review::where('user_id', $userId)->where('product_id', $productId)->where('order_id', $orderId)->exists()) {
            return back()->withErrors(['product_id' => 'You already reviewed this product for that order.']);
        }

        Review::create([
            'user_id'    => $userId,
            'product_id' => $productId,
            'order_id'   => $orderId,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Review submitted for approval.');
    }
}
