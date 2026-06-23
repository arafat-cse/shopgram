<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create() { return view('admin.coupons.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'               => 'required|string|unique:coupons,code',
            'type'               => 'required|in:fixed,percent',
            'value'              => 'required|numeric|min:0',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_discount_amount'=> 'nullable|numeric|min:0',
            'usage_limit'        => 'nullable|integer|min:0',
            'per_user_limit'     => 'nullable|integer|min:0',
            'starts_at'          => 'nullable|date',
            'ends_at'            => 'nullable|date|after_or_equal:starts_at',
            'status'             => 'required|in:active,inactive',
        ]);

        $data['code'] = strtoupper($data['code']);
        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon) { return view('admin.coupons.edit', compact('coupon')); }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code'               => 'required|string|unique:coupons,code,' . $coupon->id,
            'type'               => 'required|in:fixed,percent',
            'value'              => 'required|numeric|min:0',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_discount_amount'=> 'nullable|numeric|min:0',
            'usage_limit'        => 'nullable|integer|min:0',
            'per_user_limit'     => 'nullable|integer|min:0',
            'starts_at'          => 'nullable|date',
            'ends_at'            => 'nullable|date|after_or_equal:starts_at',
            'status'             => 'required|in:active,inactive',
        ]);

        $data['code'] = strtoupper($data['code']);
        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }

    public function show(Coupon $coupon) { return redirect()->route('admin.coupons.edit', $coupon); }
}
