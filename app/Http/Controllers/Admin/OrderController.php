<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Courier;
use App\Models\Setting;
use App\Services\ActivityLogService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::with(['user']);

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

        $oldStatus = $order->status;
        $this->orderService->updateStatus($order, $request->status, $request->note ?? '', auth()->user()?->id);

        ActivityLogService::statusChanged('Order', $order->id,
            "Changed order {$order->order_number} status from {$oldStatus} to {$request->status}",
            ['old_status' => $oldStatus, 'new_status' => $request->status]
        );

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

        ActivityLogService::updated('Order', $order->id,
            "Assigned courier to order {$order->order_number}",
            ['tracking_number' => $request->courier_tracking_number]
        );

        return back()->with('success', 'Courier assigned.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid,failed,refunded',
        ]);

        $old = $order->payment_status;
        $order->update(['payment_status' => $request->payment_status]);

        ActivityLogService::updated('Order', $order->id,
            "Changed payment status of order {$order->order_number} from {$old} to {$request->payment_status}",
            ['old_payment_status' => $old, 'new_payment_status' => $request->payment_status]
        );

        return back()->with('success', 'Payment status updated.');
    }

    public function invoiceLabels(Request $request)
    {
        $query = Order::with(['user', 'payment'])->whereNotIn('status', ['pending', 'cancelled']);

        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        return view('admin.orders.invoice-labels', compact('orders'));
    }

    public function invoice(Order $order)
    {
        $order->load(['user', 'items.product', 'payment']);
        $sender = $this->senderInfo(forPdf: false);
        return view('admin.orders.invoice', compact('order', 'sender'));
    }

    public function invoicePdf(Order $order)
    {
        $order->load(['user', 'items.product', 'payment']);
        $sender = $this->senderInfo(forPdf: true);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order', 'sender'));
        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    public function shippingLabel(Order $order)
    {
        $order->load('courier');
        $sender = $this->senderInfo(forPdf: false);
        return view('admin.orders.shipping-label', compact('order', 'sender'));
    }

    private function senderInfo(bool $forPdf): array
    {
        $logo = Setting::get('site_logo');

        return [
            'name'    => Setting::get('site_name', 'Tech'),
            // dompdf needs a local filesystem path; a browser-rendered HTML
            // view needs a normal public URL.
            'logo'    => $logo ? ($forPdf ? public_path('storage/'.$logo) : asset('storage/'.$logo)) : null,
            'phone'   => Setting::get('contact_phone'),
            'email'   => Setting::get('contact_email'),
            'address' => Setting::get('address'),
        ];
    }
}
