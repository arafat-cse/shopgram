<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::latest()->paginate(20);
        return view('admin.couriers.index', compact('couriers'));
    }

    public function create() { return view('admin.couriers.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'tracking_url' => 'nullable|url',
            'status'       => 'required|in:active,inactive',
        ]);

        Courier::create($data);
        return redirect()->route('admin.couriers.index')->with('success', 'Courier created.');
    }

    public function edit(Courier $courier) { return view('admin.couriers.edit', compact('courier')); }

    public function update(Request $request, Courier $courier)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'tracking_url' => 'nullable|url',
            'status'       => 'required|in:active,inactive',
        ]);

        $courier->update($data);
        return redirect()->route('admin.couriers.index')->with('success', 'Courier updated.');
    }

    public function destroy(Courier $courier)
    {
        $courier->delete();
        return back()->with('success', 'Courier deleted.');
    }

    public function show(Courier $courier) { return redirect()->route('admin.couriers.edit', $courier); }
}
