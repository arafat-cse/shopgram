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

        $customers = $query->withCount('orders')->latest()->paginate(20)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load([
            'orders' => fn($query) => $query->with(['items', 'payment'])->latest(),
            'addresses',
            'tickets' => fn($query) => $query->latest(),
        ]);
        $customer->loadCount([
            'orders',
            'orders as delivered_orders_count' => fn($query) => $query->where('status', 'delivered'),
            'orders as pending_orders_count' => fn($query) => $query->where('status', 'pending'),
        ]);

        $totalSpent = $customer->orders
            ->where('status', 'delivered')
            ->sum('total');

        return view('admin.customers.show', compact('customer', 'totalSpent'));
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
