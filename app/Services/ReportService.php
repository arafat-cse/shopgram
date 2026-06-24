<?php
namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function salesReport(string $from, string $to): array
    {
        $orders = Order::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('status', 'delivered')
            ->get();

        $byDay = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('status', 'delivered')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_sales'     => $orders->sum('total'),
            'order_count'     => $orders->count(),
            'avg_order_value' => $orders->count() > 0 ? $orders->avg('total') : 0,
            'by_day'          => $byDay,
        ];
    }

    public function ordersReport(string $from, string $to, ?string $status = null): array
    {
        $query = Order::with('user')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        if ($status) {
            $query->where('status', $status);
        }

        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'orders'        => $query->latest()->paginate(20),
            'status_counts' => $statusCounts,
        ];
    }

    public function productsReport(): array
    {
        $topSelling = Product::select('products.*')
            ->selectRaw('SUM(order_items.quantity) as total_qty, SUM(order_items.total_price) as total_revenue')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_qty')
            ->paginate(20);

        return ['products' => $topSelling];
    }

    public function customersReport(): array
    {
        $customers = User::role('Customer')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('orders_count')
            ->paginate(20);

        return ['customers' => $customers];
    }

    public function stockReport(): array
    {
        $lowStock = Product::whereRaw('stock_quantity <= low_stock_threshold')
            ->with('category')
            ->orderBy('stock_quantity')
            ->paginate(20);

        $outOfStock = Product::where('stock_quantity', 0)->count();

        return [
            'low_stock'    => $lowStock,
            'out_of_stock' => $outOfStock,
        ];
    }

    public function paymentsReport(string $from, string $to): array
    {
        $payments = Payment::with('order.user')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()
            ->paginate(20);

        $byMethod = Payment::selectRaw('method, COUNT(*) as count, SUM(amount) as total')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupBy('method')
            ->get();

        return [
            'payments'  => $payments,
            'by_method' => $byMethod,
        ];
    }

    public function couponsReport(): array
    {
        $coupons = Coupon::withCount('usages')
            ->withSum('usages as total_discount', DB::raw('(SELECT SUM(discount_amount) FROM orders WHERE orders.coupon_id = coupons.id)'))
            ->orderByDesc('usages_count')
            ->paginate(20);

        return ['coupons' => $coupons];
    }
}
