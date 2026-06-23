<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'brand']);

        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->brand) {
            $query->whereHas('brand', fn($q) => $q->where('slug', $request->brand));
        }

        if ($request->min_price) {
            $query->where('regular_price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('regular_price', '<=', $request->max_price);
        }

        $sort = $request->sort ?? 'latest';
        match ($sort) {
            'price_asc'  => $query->orderBy('regular_price', 'asc'),
            'price_desc' => $query->orderBy('regular_price', 'desc'),
            'popular'    => $query->orderBy('id', 'desc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->parent()->with('children')->get();
        $brands     = Brand::active()->get();

        return view('frontend.products.index', compact('products', 'categories', 'brands'));
    }

    public function show(string $slug)
    {
        $product  = Product::active()->where('slug', $slug)->with(['category', 'brand', 'images', 'variants', 'reviews.user'])->firstOrFail();
        $related  = Product::active()->where('category_id', $product->category_id)->where('id', '!=', $product->id)->take(6)->get();

        return view('frontend.products.show', compact('product', 'related'));
    }
}
