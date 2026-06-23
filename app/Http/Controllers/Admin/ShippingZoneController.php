<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::all();
        return view('admin.shipping-zones.index', compact('zones'));
    }

    public function create() { return view('admin.shipping-zones.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'charge'      => 'required|numeric|min:0',
            'free_above'  => 'nullable|numeric|min:0',
            'status'      => 'required|in:active,inactive',
        ]);

        ShippingZone::create($data);
        return redirect()->route('admin.shipping.index')->with('success', 'Shipping zone created.');
    }

    public function edit(ShippingZone $shippingZone) { return view('admin.shipping-zones.edit', compact('shippingZone')); }

    public function update(Request $request, ShippingZone $shippingZone)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'charge'      => 'required|numeric|min:0',
            'free_above'  => 'nullable|numeric|min:0',
            'status'      => 'required|in:active,inactive',
        ]);

        $shippingZone->update($data);
        return redirect()->route('admin.shipping.index')->with('success', 'Shipping zone updated.');
    }

    public function destroy(ShippingZone $shippingZone)
    {
        $shippingZone->delete();
        return back()->with('success', 'Shipping zone deleted.');
    }

    public function show(ShippingZone $shippingZone) { return redirect()->route('admin.shipping-zones.edit', $shippingZone); }
}
