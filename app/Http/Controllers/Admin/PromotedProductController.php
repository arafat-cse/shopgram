<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PromotedProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category')->orderByDesc('is_promoted')->orderBy('name');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20)->withQueryString();
        $promotedCount = Product::active()->where('is_promoted', true)->count();

        return view('admin.promoted.index', compact('products', 'promotedCount'));
    }

    public function toggle(Product $product)
    {
        $product->update(['is_promoted' => !$product->is_promoted]);

        $status = $product->is_promoted ? 'added to' : 'removed from';
        return back()->with('success', "\"{$product->name}\" {$status} promoted products.");
    }
}
