<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::active()->withCount(['products' => function($q) {
            $q->active();
        }])->get();
        return view('frontend.brands.index', compact('brands'));
    }

    public function show(string $slug, Request $request)
    {
        $brand = Brand::active()->where('slug', $slug)->firstOrFail();
        $query = Product::active()->with(['category', 'brand'])->where('brand_id', $brand->id);

        if ($request->filled('min_price')) {
            $query->where('regular_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('regular_price', '<=', $request->max_price);
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        $sort = $request->sort ?? 'latest';
        match ($sort) {
            'price_asc'  => $query->orderBy('regular_price', 'asc'),
            'price_desc' => $query->orderBy('regular_price', 'desc'),
            'popular'    => $query->orderBy('id', 'desc'),
            default      => $query->latest(),
        };

        $products    = $query->paginate(12)->withQueryString();
        $priceBounds = [
            'min' => (int) (Product::active()->where('brand_id', $brand->id)->min('regular_price') ?? 0),
            'max' => (int) (Product::active()->where('brand_id', $brand->id)->max('regular_price') ?? 0),
        ];
        $filterActionUrl = route('brand.show', $brand->slug);
        $breadcrumbTitle  = $brand->name;

        return view('frontend.products.index', compact('products', 'brand', 'priceBounds', 'filterActionUrl', 'breadcrumbTitle'));
    }
}
