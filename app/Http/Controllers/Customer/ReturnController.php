<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = auth()->user()->returns()->with('order')->paginate(10);
        return view('customer.returns.index', compact('returns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason'   => 'required|string|min:20',
        ]);

        $order = auth()->user()->orders()->findOrFail($request->order_id);

        if ($order->status !== 'delivered') {
            return back()->withErrors(['order_id' => 'Can only return delivered orders.']);
        }

        ReturnRequest::create([
            'order_id' => $order->id,
            'user_id'  => auth()->id(),
            'reason'   => $request->reason,
            'status'   => 'pending',
        ]);

        return redirect()->route('customer.returns.index')->with('success', 'Return request submitted.');
    }

    public function show(ReturnRequest $return)
    {
        abort_if($return->user_id !== auth()->id(), 403);
        $return->load('order');
        return view('customer.returns.show', compact('return'));
    }
}
