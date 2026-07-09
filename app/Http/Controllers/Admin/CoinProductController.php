<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CoinProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category')->orderByDesc('is_coin_redeemable')->orderBy('name');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20)->withQueryString();
        $redeemableCount = Product::active()->where('is_coin_redeemable', true)->count();

        return view('admin.coin-products.index', compact('products', 'redeemableCount'));
    }

    public function setPrice(Request $request, Product $product)
    {
        $data = $request->validate([
            'coin_price' => 'required|integer|min:1',
        ]);

        $product->update([
            'is_coin_redeemable' => true,
            'coin_price'         => $data['coin_price'],
        ]);

        return back()->with('success', "\"{$product->name}\" is now redeemable for {$data['coin_price']} coins.");
    }

    public function remove(Product $product)
    {
        $product->update([
            'is_coin_redeemable' => false,
            'coin_price'         => null,
        ]);

        return back()->with('success', "\"{$product->name}\" removed from coin redemption.");
    }
}
