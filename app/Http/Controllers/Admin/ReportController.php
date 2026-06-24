<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Models\Coupon;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function sales(Request $request)
    {
        $from = $request->from_date ?? now()->startOfMonth()->toDateString();
        $to   = $request->to_date ?? now()->toDateString();

        $data = $this->reportService->salesReport($from, $to);
        $orders = Order::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('status', 'delivered')
            ->latest()
            ->get();

        return view('admin.reports.sales', compact('data', 'orders', 'from', 'to'));
    }

    public function orders(Request $request)
    {
        $from   = $request->from_date ?? now()->startOfMonth()->toDateString();
        $to     = $request->to_date ?? now()->toDateString();
        $status = $request->status;

        $query = Order::with('user')->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        if ($status) $query->where('status', $status);

        $orders = $query->latest()->paginate(20);
        return view('admin.reports.orders', compact('orders', 'from', 'to', 'status'));
    }

    public function products()
    {
        $products = Product::withCount('orderItems')->orderBy('order_items_count', 'desc')->paginate(20);
        return view('admin.reports.products', compact('products'));
    }

    public function customers()
    {
        $customers = User::role('Customer')->withCount('orders')->orderBy('orders_count', 'desc')->paginate(20);
        return view('admin.reports.customers', compact('customers'));
    }

    public function stock()
    {
        $lowStock = Product::whereRaw('stock_quantity <= low_stock_threshold')->with('category')->paginate(20);
        return view('admin.reports.stock', compact('lowStock'));
    }

    public function payments(Request $request)
    {
        $payments = Payment::with('order.user')->latest()->paginate(20);
        return view('admin.reports.payments', compact('payments'));
    }

    public function coupons()
    {
        $coupons = Coupon::withCount('usages')->orderBy('usages_count', 'desc')->paginate(20);
        return view('admin.reports.coupons', compact('coupons'));
    }

    public function export(Request $request, string $type)
    {
        if ($type !== 'sales') {
            return response()->json(['message' => 'Export is available for sales report.'], 422);
        }

        $from = $request->from_date ?? now()->startOfMonth()->toDateString();
        $to = $request->to_date ?? now()->toDateString();
        $data = $this->reportService->salesReport($from, $to);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report.csv"',
        ];

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Orders', 'Revenue']);

            foreach ($data['by_day'] as $row) {
                fputcsv($handle, [$row->date, $row->orders, $row->revenue]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
