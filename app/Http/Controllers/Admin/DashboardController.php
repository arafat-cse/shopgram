<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sales'      => Order::where('status', 'delivered')->sum('total'),
            'today_sales'      => Order::where('status', 'delivered')->whereDate('created_at', today())->sum('total'),
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::where('status', 'pending')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_customers'  => User::role('Customer')->count(),
            'low_stock'        => Product::where('stock_quantity', '<=', DB::raw('low_stock_threshold'))->count(),
        ];

        $monthlySales = Order::where('status', 'delivered')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $recentOrders   = Order::with('user')->latest()->take(10)->get();
        $lowStockItems  = Product::where('stock_quantity', '<=', DB::raw('low_stock_threshold'))->take(10)->get();
        $topProducts    = Product::withCount('orderItems')->orderBy('order_items_count', 'desc')->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'monthlySales', 'recentOrders', 'lowStockItems', 'topProducts'));
    }
}
