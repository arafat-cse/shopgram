<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q', '');
        $products = Product::active()
            ->where('name', 'like', "%{$q}%")
            ->orWhere('sku', 'like', "%{$q}%")
            ->with('category')
            ->paginate(12);

        return view('frontend.search.index', compact('products', 'q'));
    }

    public function suggestions(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $products = Product::active()
            ->where('name', 'like', "%{$q}%")
            ->select('id', 'name', 'slug', 'thumbnail', 'regular_price', 'sale_price')
            ->take(6)
            ->get();

        return response()->json($products);
    }
}
