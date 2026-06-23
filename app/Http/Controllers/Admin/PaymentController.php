<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('order.user');

        if ($request->method) $query->where('method', $request->method);
        if ($request->status) $query->where('status', $request->status);
        if ($request->from_date) $query->whereDate('created_at', '>=', $request->from_date);
        if ($request->to_date) $query->whereDate('created_at', '<=', $request->to_date);

        $payments = $query->latest()->paginate(20)->withQueryString();
        return view('admin.payments.index', compact('payments'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status'         => 'required|in:pending,paid,failed,refunded',
            'transaction_id' => 'nullable|string',
        ]);

        $payment->update($request->only('status', 'transaction_id'));

        if ($request->status === 'paid') {
            $payment->order->update(['payment_status' => 'paid']);
        } elseif ($request->status === 'refunded') {
            $payment->order->update(['payment_status' => 'refunded']);
        }

        return back()->with('success', 'Payment status updated.');
    }
}
