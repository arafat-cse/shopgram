<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;

class BrandController extends Controller
{
    public function show(string $slug)
    {
        $brand    = Brand::active()->where('slug', $slug)->firstOrFail();
        $products = Product::active()->where('brand_id', $brand->id)->paginate(12);

        return view('frontend.products.index', compact('products', 'brand'));
    }
}
