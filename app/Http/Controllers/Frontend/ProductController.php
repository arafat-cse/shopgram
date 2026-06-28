<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\RecentlyViewedProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'brand']);

        if ($request->category) {
            $selectedCategory = Category::active()
                ->with('children')
                ->where('slug', $request->category)
                ->first();

            if ($selectedCategory) {
                $categoryIds = collect([$selectedCategory->id])
                    ->merge($selectedCategory->children->pluck('id'))
                    ->all();

                $query->whereIn('category_id', $categoryIds);
            }
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
        $categories = Category::active()
            ->parent()
            ->with(['children' => fn($query) => $query->active()->orderBy('name')])
            ->orderBy('name')
            ->get();
        $brands     = Brand::active()->get();

        return view('frontend.products.index', compact('products', 'categories', 'brands'));
    }

    public function show(string $slug, RecentlyViewedProductService $recentlyViewed)
    {
        $product = Product::active()->where('slug', $slug)->with(['category', 'brand', 'images', 'variants', 'reviews.user'])->firstOrFail();
        $recentProducts = $recentlyViewed->get($product);
        $recentlyViewed->record($product);
        $related = Product::active()->where('category_id', $product->category_id)->where('id', '!=', $product->id)->take(6)->get();

        $soldLast24h = OrderItem::where('product_id', $product->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->sum('quantity');

        $canReview = false;
        $unreviewedOrder = null;
        if (auth()->check()) {
            $reviewedOrderIds = \App\Models\Review::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->pluck('order_id');

            $unreviewedOrder = \App\Models\Order::where('user_id', auth()->id())
                ->where('status', 'delivered')
                ->whereHas('items', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->whereNotIn('id', $reviewedOrderIds)
                ->latest()
                ->first();

            $canReview = $unreviewedOrder !== null;
        }

        return view('frontend.products.show', compact('product', 'related', 'recentProducts', 'soldLast24h', 'canReview', 'unreviewedOrder'));
    }

    public function quickView(string $slug)
    {
        $product = Product::active()->where('slug', $slug)->with(['category', 'variants', 'images'])->firstOrFail();
        return view('frontend.products.quickview', compact('product'));
    }
}
