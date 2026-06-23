<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $products   = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $brands     = Brand::active()->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'category_id'        => 'required|exists:categories,id',
            'brand_id'           => 'nullable|exists:brands,id',
            'sku'                => 'nullable|string|unique:products,sku',
            'short_description'  => 'nullable|string',
            'description'        => 'nullable|string',
            'specification'      => 'nullable|string',
            'regular_price'      => 'required|numeric|min:0',
            'sale_price'         => 'nullable|numeric|min:0',
            'purchase_price'     => 'nullable|numeric|min:0',
            'stock_quantity'     => 'required|integer|min:0',
            'low_stock_threshold'=> 'nullable|integer|min:0',
            'status'             => 'required|in:active,inactive,draft',
            'is_featured'        => 'boolean',
            'is_new_arrival'     => 'boolean',
            'is_best_selling'    => 'boolean',
            'thumbnail'          => 'nullable|image|max:2048',
            'seo_title'          => 'nullable|string|max:255',
            'seo_description'    => 'nullable|string',
            'seo_keywords'       => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . uniqid();
        $data['is_featured']     = $request->boolean('is_featured');
        $data['is_new_arrival']  = $request->boolean('is_new_arrival');
        $data['is_best_selling'] = $request->boolean('is_best_selling');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        $product = Product::create($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $i => $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $img->store('products', 'public'),
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $brands     = Brand::active()->get();
        $product->load('images', 'variants');
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'category_id'        => 'required|exists:categories,id',
            'brand_id'           => 'nullable|exists:brands,id',
            'sku'                => 'nullable|string|unique:products,sku,' . $product->id,
            'short_description'  => 'nullable|string',
            'description'        => 'nullable|string',
            'specification'      => 'nullable|string',
            'regular_price'      => 'required|numeric|min:0',
            'sale_price'         => 'nullable|numeric|min:0',
            'purchase_price'     => 'nullable|numeric|min:0',
            'stock_quantity'     => 'required|integer|min:0',
            'low_stock_threshold'=> 'nullable|integer|min:0',
            'status'             => 'required|in:active,inactive,draft',
            'is_featured'        => 'boolean',
            'is_new_arrival'     => 'boolean',
            'is_best_selling'    => 'boolean',
            'thumbnail'          => 'nullable|image|max:2048',
            'seo_title'          => 'nullable|string|max:255',
            'seo_description'    => 'nullable|string',
            'seo_keywords'       => 'nullable|string',
        ]);

        $data['is_featured']     = $request->boolean('is_featured');
        $data['is_new_arrival']  = $request->boolean('is_new_arrival');
        $data['is_best_selling'] = $request->boolean('is_best_selling');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate(['images.*' => 'image|max:2048']);

        $count = $product->images()->count();
        foreach ($request->file('images') as $i => $img) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $img->store('products', 'public'),
                'sort_order' => $count + $i,
                'is_primary' => $count === 0 && $i === 0,
            ]);
        }

        return back()->with('success', 'Images uploaded.');
    }

    public function deleteImage(ProductImage $image)
    {
        $image->delete();
        return back()->with('success', 'Image deleted.');
    }

    public function show(Product $product) { return redirect()->route('admin.products.edit', $product); }
}
