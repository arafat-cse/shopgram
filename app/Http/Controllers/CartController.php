<?php
namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CouponService;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService   $cartService,
        private CouponService $couponService
    ) {}

    public function index()
    {
        $user      = auth()->user();
        $items     = $this->cartService->getItems($user);
        $subtotal  = $this->cartService->getSubtotal($user);
        $coupon    = session('cart_coupon');
        $discount  = session('cart_discount', 0);
        $zones     = ShippingZone::active()->get();

        return view('frontend.cart.index', compact('items', 'subtotal', 'coupon', 'discount', 'zones'));
    }

    public function add(Request $request)
    {
        if (!auth()->check()) {
            session([
                'pending_action'     => $request->buy_now ? 'buy_now' : 'add_to_cart',
                'pending_product_id' => $request->product_id,
                'pending_variant_id' => $request->variant_id,
                'pending_quantity'   => $request->quantity ?? 1,
                'intended_url'       => url()->previous(),
            ]);

            return redirect()->route('login')->with('info', 'Please login first to continue.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $this->cartService->addItem(
            auth()->user(),
            $request->product_id,
            $request->variant_id,
            $request->quantity ?? 1
        );

        if ($request->buy_now) {
            return redirect()->route('checkout.index');
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        $this->cartService->updateQuantity(auth()->user(), $request->cart_item_id, $request->quantity);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(int $id)
    {
        $this->cartService->removeItem(auth()->user(), $id);
        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $subtotal = $this->cartService->getSubtotal(auth()->user());
        $result   = $this->couponService->validate($request->coupon_code, auth()->user(), $subtotal);

        if (!$result['valid']) {
            return back()->withErrors(['coupon_code' => $result['message']]);
        }

        session([
            'cart_coupon'   => $result['coupon'],
            'cart_discount' => $result['discount'],
        ]);

        return back()->with('success', $result['message']);
    }

    public function removeCoupon()
    {
        session()->forget(['cart_coupon', 'cart_discount']);
        return back()->with('success', 'Coupon removed.');
    }
}
