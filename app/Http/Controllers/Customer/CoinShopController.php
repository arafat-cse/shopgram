<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CoinService;
use Illuminate\Http\Request;

class CoinShopController extends Controller
{
    public function __construct(private CoinService $coinService) {}

    public function index()
    {
        $user = auth()->user();
        $products = Product::active()->coinRedeemable()
            ->where('stock_quantity', '>', 0)
            ->latest()
            ->paginate(12);

        $transactions = $user->coinTransactions()->paginate(15, ['*'], 'transactions_page');

        return view('customer.coins.index', compact('products', 'transactions', 'user'));
    }

    public function redeem(Request $request, Product $product)
    {
        $user = auth()->user();
        $address = $user->defaultAddress ?? $user->addresses()->latest()->first();

        if (!$address) {
            return back()->with('error', 'Please add a delivery address before redeeming a product.');
        }

        try {
            $order = $this->coinService->redeem($user, $product, [
                'name'         => $address->name,
                'phone'        => $address->phone,
                'address_line' => $address->address_line,
                'city'         => $address->city,
                'district'     => $address->district,
                'zip'          => $address->zip,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('customer.orders.show', $order)
            ->with('success', "Redeemed successfully! Order #{$order->order_number} placed.");
    }
}
