<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    private array $completedStatuses = ['delivered', 'completed'];

    public function getDateRange(Request $request): array
    {
        $filter = $request->input('filter', 'this_month');
        $today = now();

        [$from, $to] = match ($filter) {
            'today' => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
            'yesterday' => [$today->copy()->subDay()->startOfDay(), $today->copy()->subDay()->endOfDay()],
            'last_7_days' => [$today->copy()->subDays(6)->startOfDay(), $today->copy()->endOfDay()],
            'last_month' => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()],
            'this_year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            'custom' => [
                $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : $today->copy()->startOfMonth(),
                $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : $today->copy()->endOfDay(),
            ],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfDay()],
        };

        return compact('filter', 'from', 'to');
    }

    public function getAnalyticsData(Request $request): array
    {
        $range = $this->getDateRange($request);
        $from = $range['from'];
        $to = $range['to'];

        $completedOrders = $this->ordersInRange($from, $to)
            ->whereIn('status', $this->completedStatuses);

        $soldItems = $this->completedOrderItemsInRange($from, $to)
            ->select('order_items.*', DB::raw('COALESCE(products.purchase_price, 0) as product_purchase_price'))
            ->get();
        $purchaseCost = $this->calculatePurchaseCost($soldItems);
        $grossProfit = $this->calculateProfit($soldItems);
        $loss = $this->calculateLoss($soldItems);
        $totalSales = (float) (clone $completedOrders)->sum('total');

        return [
            'range' => $range,
            'cards' => [
                'total_products' => Product::count(),
                'total_product_purchase_price' => (float) Product::sum(DB::raw('COALESCE(purchase_price, 0) * stock_quantity')),
                'total_product_selling_price' => (float) Product::sum(DB::raw('COALESCE(sale_price, regular_price, 0) * stock_quantity')),
                'total_orders' => $this->ordersInRange($from, $to)->count(),
                'total_sales' => $totalSales,
                'total_purchase_cost_sold' => $purchaseCost,
                'total_profit' => max($grossProfit, 0),
                'total_loss' => $loss + max($purchaseCost - $totalSales, 0),
                'pending_orders' => $this->ordersInRange($from, $to)->where('status', 'pending')->count(),
                'processing_orders' => $this->ordersInRange($from, $to)->where('status', 'processing')->count(),
                'completed_orders' => $this->ordersInRange($from, $to)->whereIn('status', $this->completedStatuses)->count(),
                'cancelled_orders' => $this->ordersInRange($from, $to)->where('status', 'cancelled')->count(),
                'total_customers' => User::whereHas('orders', fn($query) => $query->whereBetween('created_at', [$from, $to]))->count(),
                'low_stock_products' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0)->count(),
                'out_of_stock_products' => Product::where('stock_quantity', 0)->count(),
                'today_sales' => $this->completedOrdersForPeriod(now()->startOfDay(), now()->endOfDay())->sum('total'),
                'this_month_sales' => $this->completedOrdersForPeriod(now()->startOfMonth(), now()->endOfMonth())->sum('total'),
                'this_year_sales' => $this->completedOrdersForPeriod(now()->startOfYear(), now()->endOfYear())->sum('total'),
            ],
            'monthlySales' => $this->getSalesChart($from, $to),
            'monthlyProfit' => $this->getProfitChart($from, $to),
            'orderStatus' => $this->getOrderStatusChart($from, $to),
            'topSellingProducts' => $this->getTopSellingProducts($from, $to),
            'recentOrders' => $this->getRecentOrders($from, $to),
            'lowStockProducts' => $this->getLowStockProducts(),
        ];
    }

    public function getSalesChart(Carbon $from, Carbon $to): array
    {
        $sales = $this->ordersInRange($from, $to)
            ->whereIn('status', $this->completedStatuses)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return $this->fillMonthlySeries($from, $to, $sales);
    }

    public function getProfitChart(Carbon $from, Carbon $to): array
    {
        $items = $this->completedOrderItemsInRange($from, $to)
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as month")
            ->selectRaw('SUM((COALESCE(order_items.selling_price, order_items.unit_price, 0) - COALESCE(order_items.purchase_price, products.purchase_price, 0)) * order_items.quantity) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return $this->fillMonthlySeries($from, $to, $items);
    }

    public function getOrderStatusChart(Carbon $from, Carbon $to): array
    {
        $counts = $this->ordersInRange($from, $to)
            ->selectRaw("CASE WHEN status IN ('delivered','completed') THEN 'completed' ELSE status END as status_group, COUNT(*) as total")
            ->whereIn('status', ['pending', 'processing', 'delivered', 'completed', 'cancelled'])
            ->groupBy('status_group')
            ->pluck('total', 'status_group');

        return [
            'Pending' => (int) ($counts['pending'] ?? 0),
            'Processing' => (int) ($counts['processing'] ?? 0),
            'Completed' => (int) ($counts['completed'] ?? 0),
            'Cancelled' => (int) ($counts['cancelled'] ?? 0),
        ];
    }

    public function getTopSellingProducts(Carbon $from, Carbon $to): Collection
    {
        return $this->completedOrderItemsInRange($from, $to)
            ->selectRaw('products.name, products.sku, SUM(order_items.quantity) as sold_quantity, SUM(order_items.total_price) as sales_amount')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('sold_quantity')
            ->limit(10)
            ->get();
    }

    public function getRecentOrders(Carbon $from, Carbon $to): Collection
    {
        return $this->ordersInRange($from, $to)
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getLowStockProducts(): Collection
    {
        return Product::with('category')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();
    }

    private function ordersInRange(Carbon $from, Carbon $to): Builder
    {
        return Order::query()->whereBetween('created_at', [$from, $to]);
    }

    private function completedOrdersForPeriod(Carbon $from, Carbon $to): Builder
    {
        return $this->ordersInRange($from, $to)->whereIn('status', $this->completedStatuses);
    }

    private function completedOrderItemsInRange(Carbon $from, Carbon $to): Builder
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereIn('orders.status', $this->completedStatuses);
    }

    private function calculatePurchaseCost(Collection $items): float
    {
        return (float) $items->sum(fn($item) => (float) ($item->purchase_price ?: $item->product_purchase_price ?: 0) * $item->quantity);
    }

    private function calculateProfit(Collection $items): float
    {
        return (float) $items->sum(function ($item) {
            $selling = (float) ($item->selling_price ?: $item->unit_price ?: 0);
            $purchase = (float) ($item->purchase_price ?: $item->product_purchase_price ?: 0);
            return max($selling - $purchase, 0) * $item->quantity;
        });
    }

    private function calculateLoss(Collection $items): float
    {
        return (float) $items->sum(function ($item) {
            $selling = (float) ($item->selling_price ?: $item->unit_price ?: 0);
            $purchase = (float) ($item->purchase_price ?: $item->product_purchase_price ?: 0);
            return max($purchase - $selling, 0) * $item->quantity;
        });
    }

    private function fillMonthlySeries(Carbon $from, Carbon $to, Collection $values): array
    {
        $labels = [];
        $data = [];
        $cursor = $from->copy()->startOfMonth();
        $end = $to->copy()->startOfMonth();

        while ($cursor <= $end) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->format('M Y');
            $data[] = round((float) ($values[$key] ?? 0), 2);
            $cursor->addMonth();
        }

        return compact('labels', 'data');
    }
}
