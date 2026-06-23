<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('Customer');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) $query->where('status', $request->status);

        $customers = $query->latest()->paginate(20)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load(['orders', 'addresses', 'tickets']);
        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $customer->update($request->only('status'));
        return back()->with('success', 'Customer updated.');
    }

    public function toggleStatus(User $customer)
    {
        $customer->update([
            'status' => $customer->status === 'active' ? 'blocked' : 'active',
        ]);

        return back()->with('success', 'Customer status updated.');
    }
}
