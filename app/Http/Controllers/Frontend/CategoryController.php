<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(string $slug, Request $request)
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();
        $query = Product::active()->with(['category', 'brand'])
            ->where(function ($q) use ($category) {
                $q->where('category_id', $category->id)
                  ->orWhereIn('category_id', $category->children->pluck('id'));
            });

        if ($request->filled('brand')) {
            $query->whereHas('brand', fn($q) => $q->whereIn('slug', (array) $request->brand));
        }
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
        $brands      = Brand::active()->get();
        $priceBounds = [
            'min' => (int) (Product::active()->min('regular_price') ?? 0),
            'max' => (int) (Product::active()->max('regular_price') ?? 0),
        ];

        $filterActionUrl = route('category.show', $category->slug);
        $breadcrumbTitle  = $category->name;

        return view('frontend.products.index', compact('products', 'category', 'brands', 'priceBounds', 'filterActionUrl', 'breadcrumbTitle'));
    }
}
