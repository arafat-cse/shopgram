<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShippingZone;
use App\Services\CartService;
use App\Services\OrderService;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService  $cartService,
        private OrderService $orderService
    ) {}

    public function index()
    {
        $user      = auth()->user();
        $items     = $this->cartService->getItems($user);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('info', 'Your cart is empty.');
        }

        $addresses = $user->addresses()->latest()->get();
        $subtotal  = $this->cartService->getSubtotal($user);
        $coupon    = session('cart_coupon');
        $discount  = session('cart_discount', 0);
        $zones     = ShippingZone::active()->get();

        return view('frontend.checkout.index', compact('items', 'addresses', 'subtotal', 'coupon', 'discount', 'zones'));
    }

    public function placeOrder(CheckoutRequest $request)
    {
        $user = auth()->user();

        $billingAddress = [
            'name'         => $request->billing_name,
            'phone'        => $request->billing_phone,
            'address_line' => $request->billing_address,
            'city'         => $request->billing_city,
            'district'     => $request->billing_district,
            'zip'          => $request->billing_zip,
        ];

        $shippingAddress = $request->same_as_billing
            ? $billingAddress
            : [
                'name'         => $request->shipping_name,
                'phone'        => $request->shipping_phone,
                'address_line' => $request->shipping_address,
                'city'         => $request->shipping_city,
                'district'     => $request->shipping_district,
                'zip'          => $request->shipping_zip,
            ];

        $coupon         = session('cart_coupon');
        $shippingZoneId = $request->shipping_zone_id;
        $shippingCharge = 0;

        if ($shippingZoneId) {
            $zone           = ShippingZone::find($shippingZoneId);
            $subtotal       = $this->cartService->getSubtotal($user);
            $shippingCharge = $zone ? $zone->calculateCharge($subtotal) : 0;
        }

        $screenshot = null;
        if ($request->hasFile('payment_screenshot')) {
            $screenshot = $request->file('payment_screenshot')->store('payments', 'public');
        }

        $order = $this->orderService->placeOrder($user, [
            'billing_address'    => $billingAddress,
            'shipping_address'   => $shippingAddress,
            'shipping_zone_id'   => $shippingZoneId,
            'shipping_charge'    => $shippingCharge,
            'payment_method'     => $request->payment_method,
            'delivery_method'    => $request->delivery_method,
            'order_note'         => $request->order_note,
            'coupon_id'          => $coupon?->id,
            'payment_screenshot' => $screenshot,
        ]);

        session()->forget(['cart_coupon', 'cart_discount']);

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully!');
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('frontend.checkout.success', compact('order'));
    }
}
