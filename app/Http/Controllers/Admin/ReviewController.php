<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);
        if ($request->status) $query->where('status', $request->status);
        $reviews = $query->latest()->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, Review $review)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $review->update(['status' => $request->status]);
        return back()->with('success', 'Review ' . $request->status . '.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
