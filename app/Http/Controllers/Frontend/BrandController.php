<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::active()->withCount(['products' => function($q) {
            $q->active();
        }])->get();
        return view('frontend.brands.index', compact('brands'));
    }

    public function show(string $slug)
    {
        $brand    = Brand::active()->where('slug', $slug)->firstOrFail();
        $products = Product::active()->where('brand_id', $brand->id)->paginate(12);

        return view('frontend.products.index', compact('products', 'brand'));
    }
}
