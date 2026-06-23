<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'orders'    => $user->orders()->count(),
            'wishlist'  => $user->wishlist()->count(),
            'pending'   => $user->orders()->where('status', 'pending')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
        ];

        $recentOrders = $user->orders()->latest()->take(5)->get();

        return view('customer.dashboard', compact('stats', 'recentOrders'));
    }
}
