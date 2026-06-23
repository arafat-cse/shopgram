<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Courier;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->status) $query->where('status', $request->status);
        if ($request->payment_status) $query->where('payment_status', $request->payment_status);
        if ($request->from_date) $query->whereDate('created_at', '>=', $request->from_date);
        if ($request->to_date) $query->whereDate('created_at', '<=', $request->to_date);
        if ($request->search) {
            $query->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variant', 'statusHistories.updatedBy', 'payment', 'courier', 'shippingZone']);
        $couriers = Courier::active()->get();
        return view('admin.orders.show', compact('order', 'couriers'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate(['status' => 'required']);
        $order->update($request->only(['status']));
        return back()->with('success', 'Order updated.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,packed,shipped,out_for_delivery,delivered,cancelled,returned,refunded',
            'note'   => 'nullable|string',
        ]);

        $this->orderService->updateStatus($order, $request->status, $request->note ?? '', auth()->id());

        return back()->with('success', 'Order status updated.');
    }

    public function assignCourier(Request $request, Order $order)
    {
        $request->validate([
            'courier_id'             => 'required|exists:couriers,id',
            'courier_tracking_number'=> 'nullable|string|max:255',
        ]);

        $order->update([
            'courier_id'              => $request->courier_id,
            'courier_tracking_number' => $request->courier_tracking_number,
        ]);

        return back()->with('success', 'Courier assigned.');
    }

    public function invoice(Order $order)
    {
        $order->load(['user', 'items.product', 'payment']);
        return view('admin.orders.invoice', compact('order'));
    }

    public function invoicePdf(Order $order)
    {
        $order->load(['user', 'items.product', 'payment']);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
