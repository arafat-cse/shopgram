<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private WishlistService $wishlistService) {}

    public function index()
    {
        $wishlist = auth()->user()->wishlist()->with('product.category')->paginate(12);
        return view('customer.wishlist.index', compact('wishlist'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            session([
                'pending_action'     => 'add_to_wishlist',
                'pending_product_id' => $request->product_id,
                'intended_url'       => url()->previous(),
            ]);
            return redirect()->route('login')->with('info', 'Please login first to continue.');
        }

        $request->validate(['product_id' => 'required|exists:products,id']);

        $this->wishlistService->add(auth()->user(), $request->product_id);

        return back()->with('success', 'Added to wishlist.');
    }

    public function destroy(Wishlist $wishlist)
    {
        abort_if($wishlist->user_id !== auth()->id(), 403);
        $wishlist->delete();
        return back()->with('success', 'Removed from wishlist.');
    }
}
