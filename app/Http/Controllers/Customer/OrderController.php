<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
