<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Services\RecentlyViewedProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'brand']);
        $this->applyFilters($query, $request);

        $products = $query->paginate(12)->withQueryString();

        return view('frontend.products.index', array_merge(
            compact('products'),
            $this->filterViewData()
        ));
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            if ($category) {
                $categoryIds = $category->parent_id === null
                    ? collect([$category->id])->merge($category->children->pluck('id'))->all()
                    : [$category->id];
                $query->whereIn('category_id', $categoryIds);
            }
        }

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
    }

    /**
     * Shared sidebar filter data (price bounds, brands, category tree) used
     * by every product listing page that includes the filter partial.
     */
    private function filterViewData(): array
    {
        return [
            'brands'      => Brand::active()->get(),
            'priceBounds' => [
                'min' => (int) (Product::active()->min('regular_price') ?? 0),
                'max' => (int) (Product::active()->max('regular_price') ?? 0),
            ],
            'categories'  => Category::active()
                ->parent()
                ->with(['children' => fn($q) => $q->active()->orderBy('name')])
                ->orderBy('name')
                ->get(),
        ];
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

    public function bestSellers(Request $request)
    {
        $query = Product::active()->bestSelling()->with(['category', 'brand']);
        $this->applyFilters($query, $request);

        $products = $query->paginate(12)->withQueryString();
        $pageTitle = $breadcrumbTitle = 'Best Sellers';
        $filterActionUrl = route('products.bestsellers');

        return view('frontend.products.index', array_merge(
            compact('products', 'pageTitle', 'breadcrumbTitle', 'filterActionUrl'),
            $this->filterViewData()
        ));
    }

    public function offers(Request $request)
    {
        $query = Product::active()->whereNotNull('sale_price')->with(['category', 'brand']);
        $this->applyFilters($query, $request);

        $products = $query->paginate(12)->withQueryString();
        $pageTitle = $breadcrumbTitle = 'Special Offers';
        $filterActionUrl = route('products.offers');

        return view('frontend.products.index', array_merge(
            compact('products', 'pageTitle', 'breadcrumbTitle', 'filterActionUrl'),
            $this->filterViewData()
        ));
    }

    public function newArrivals(Request $request)
    {
        $query = Product::active()->newArrivals()->with(['category', 'brand']);
        $this->applyFilters($query, $request);

        $products = $query->paginate(12)->withQueryString();
        $pageTitle = $breadcrumbTitle = 'New Arrivals';
        $filterActionUrl = route('products.new-arrivals');

        return view('frontend.products.index', array_merge(
            compact('products', 'pageTitle', 'breadcrumbTitle', 'filterActionUrl'),
            $this->filterViewData()
        ));
    }

    public function quickView(string $slug)
    {
        $product = Product::active()->where('slug', $slug)->with(['category', 'variants', 'images'])->firstOrFail();
        return view('frontend.products.quickview', compact('product'));
    }
}
