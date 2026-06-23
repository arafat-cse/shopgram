<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();
        $products = Product::active()
            ->where(function ($q) use ($category) {
                $q->where('category_id', $category->id)
                  ->orWhereIn('category_id', $category->children->pluck('id'));
            })
            ->paginate(12);

        return view('frontend.products.index', compact('products', 'category'));
    }
}
