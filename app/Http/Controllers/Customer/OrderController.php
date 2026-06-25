<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load(['items.product', 'items.variant', 'statusHistories', 'payment', 'courier']);
        return view('customer.orders.show', compact('order'));
    }

    public function invoicePdf(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load(['items.product', 'items.variant', 'payment', 'courier']);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    public function reorder(Order $order, CartService $cart)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('items.product', 'items.variant');
        $user  = auth()->user();
        $added = 0;

        foreach ($order->items as $item) {
            if (!$item->product || !$item->product->isInStock()) continue;
            $cart->addItem($user, $item->product_id, $item->variant_id, $item->quantity);
            $added++;
        }

        if ($added === 0) {
            return back()->with('error', 'None of the items are currently in stock.');
        }

        return redirect()->route('cart.index')->with('success', $added . ' item(s) added to cart from your previous order.');
    }

    public function tracking()
    {
        return view('customer.orders.tracking');
    }

    public function trackByNumber(Request $request)
    {
        $request->validate(['order_number' => 'required|string']);

        $order = Order::where('order_number', $request->order_number)
            ->with(['statusHistories', 'courier'])
            ->first();

        if (!$order) {
            return back()->withErrors(['order_number' => 'Order not found.']);
        }

        return view('customer.orders.tracking', compact('order'));
    }
}
