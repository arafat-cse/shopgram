<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\RecentlyViewedProductService;

class DashboardController extends Controller
{
    public function index(RecentlyViewedProductService $recentlyViewed)
    {
        $user = auth()->user();

        $stats = [
            'orders'      => $user->orders()->count(),
            'active'      => $user->orders()->whereNotIn('status', ['delivered', 'cancelled', 'returned', 'refunded'])->count(),
            'delivered'   => $user->orders()->where('status', 'delivered')->count(),
            'total_spent' => (float) $user->orders()->where('status', 'delivered')->sum('total'),
        ];

        $recentOrders = $user->orders()->latest()->take(5)->get();
        $recentlyViewedProducts = $recentlyViewed->get();

        $wishlistCount = $user->wishlist()->count();
        $openTicketCount = $user->tickets()->open()->count();
        $needsAddress = !$user->addresses()->exists();
        $needsPhone = str_starts_with((string) $user->phone, 'gtmp_');

        return view('customer.dashboard', compact(
            'stats', 'recentOrders', 'recentlyViewedProducts',
            'wishlistCount', 'openTicketCount', 'needsAddress', 'needsPhone'
        ));
    }
}
