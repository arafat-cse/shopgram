<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;

class HomeController extends Controller
{
    public function index(CartService $cartService)
    {
        $banners      = Banner::active()->hero()->orderBy('sort_order')->get();
        $promoBanners = Banner::active()->promo()->orderBy('sort_order')->take(2)->get();
        $featuredCategoryNames = [
            'Oil & Ghee',
            'Organic',
            'Honey',
            'Dates',
            'Spices',
            'Nuts & Seeds',
            'Beverage',
            'Rice',
            'Flours & Lentils',
            'Functional Food',
        ];

        $categories   = Category::active()
            ->parent()
            ->with(['products', 'children.products'])
            ->orderByRaw(
                'CASE name ' . collect($featuredCategoryNames)
                    ->map(fn($name, $index) => "WHEN ? THEN {$index}")
                    ->implode(' ') . ' ELSE 999 END',
                $featuredCategoryNames
            )
            ->orderBy('name')
            ->get();
        $brands       = Brand::active()->take(12)->get();
        $featured     = Product::active()->featured()->with(['category', 'brand'])->take(8)->get();
        $newArrivals  = Product::active()->newArrivals()->with('category')->take(8)->get();
        $bestSelling  = Product::active()->bestSelling()->with(['category', 'brand'])->take(4)->get();
        $discounts    = Product::active()->whereNotNull('sale_price')->with('category')->take(8)->get();
        $allProducts  = Product::active()->with('category')->latest()->take(12)->get();
        $cartSubtotal = auth()->check() ? $cartService->getSubtotal(auth()->user()) : 0;

        return view('frontend.home.index', compact(
            'banners',
            'promoBanners',
            'categories',
            'brands',
            'featured',
            'newArrivals',
            'bestSelling',
            'discounts',
            'allProducts',
            'cartSubtotal'
        ));
    }
}
