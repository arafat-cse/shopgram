<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\ActivityLogService;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function create(Request $request)
    {
        $categories = Category::active()->get();
        $brands     = Brand::active()->get();
        $sourceProduct = null;
        $prefill = [];

        if ($request->filled('duplicate')) {
            $sourceProduct = Product::with(['images', 'variants'])->findOrFail($request->integer('duplicate'));
            $prefill = [
                'duplicate_product_id' => $sourceProduct->id,
                'name' => 'Copy of ' . $sourceProduct->name,
                'category_id' => $sourceProduct->category_id,
                'brand_id' => $sourceProduct->brand_id,
                'sku' => $this->uniqueSku($sourceProduct->sku),
                'short_description' => $sourceProduct->short_description,
                'description' => $sourceProduct->description,
                'regular_price' => $sourceProduct->regular_price,
                'sale_price' => $sourceProduct->sale_price,
                'purchase_price' => $sourceProduct->purchase_price,
                'stock_quantity' => $sourceProduct->stock_quantity,
                'low_stock_threshold' => $sourceProduct->low_stock_threshold,
                'status' => 'draft',
                'is_featured' => false,
                'is_new_arrival' => false,
                'is_best_selling' => false,
                'seo_title' => $sourceProduct->seo_title,
                'seo_description' => $sourceProduct->seo_description,
                'seo_keywords' => $sourceProduct->seo_keywords,
                'video_url' => $sourceProduct->video_url,
            ];
        }

        return view('admin.products.create', compact('categories', 'brands', 'sourceProduct', 'prefill'));
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
            'is_promoted'        => 'boolean',
            'thumbnail'          => 'nullable|image|max:16384',
            'video_url'          => 'nullable|url|max:255',
            'gallery'            => 'nullable|array|max:8',
            'gallery.*'          => 'image|max:16384',
            'seo_title'          => 'nullable|string|max:255',
            'seo_description'    => 'nullable|string',
            'seo_keywords'       => 'nullable|string',
            'duplicate_product_id'=> 'nullable|exists:products,id',
        ]);

        $sourceProduct = null;
        if (!empty($data['duplicate_product_id'])) {
            $sourceProduct = Product::with(['images', 'variants'])->find($data['duplicate_product_id']);
        }
        unset($data['duplicate_product_id']);

        $data['slug'] = Str::slug($data['name']) . '-' . uniqid();
        $data['is_featured']     = $request->boolean('is_featured');
        $data['is_new_arrival']  = $request->boolean('is_new_arrival');
        $data['is_best_selling'] = $request->boolean('is_best_selling');
        $data['is_promoted']     = $request->boolean('is_promoted');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        } elseif ($sourceProduct) {
            $data['thumbnail'] = $this->copyPublicFile($sourceProduct->thumbnail);
        }

        $product = Product::create($data);

        if ($sourceProduct) {
            $this->copyGalleryImages($sourceProduct, $product);
            $this->copyVariants($sourceProduct, $product);
        }

        $this->storeGalleryImages($request, $product);

        ActivityLogService::created('Product', $product->id, "Created product \"{$product->name}\"");

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
            'is_promoted'        => 'boolean',
            'thumbnail'          => 'nullable|image|max:16384',
            'video_url'          => 'nullable|url|max:255',
            'gallery'            => 'nullable|array|max:8',
            'gallery.*'          => 'image|max:16384',
            'seo_title'          => 'nullable|string|max:255',
            'seo_description'    => 'nullable|string',
            'seo_keywords'       => 'nullable|string',
        ]);

        $data['is_featured']     = $request->boolean('is_featured');
        $data['is_new_arrival']  = $request->boolean('is_new_arrival');
        $data['is_best_selling'] = $request->boolean('is_best_selling');
        $data['is_promoted']     = $request->boolean('is_promoted');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        $product->update($data);
        $this->storeGalleryImages($request, $product);

        ActivityLogService::updated('Product', $product->id, "Updated product \"{$product->name}\"");

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        ActivityLogService::deleted('Product', $product->id, "Deleted product \"{$product->name}\"");
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    public function duplicate(Product $product)
    {
        return redirect()
            ->route('admin.products.create', ['duplicate' => $product->id])
            ->with('info', 'Product details copied. Click Save Product to create the duplicate.');
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate([
            'images' => 'required|array|max:8',
            'images.*' => 'image|max:16384',
        ]);

        $this->storeGalleryImages($request, $product, 'images');

        return back()->with('success', 'Images uploaded.');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Image deleted.');
    }

    public function show(Product $product) { return redirect()->route('admin.products.edit', $product); }

    private function storeGalleryImages(Request $request, Product $product, string $field = 'gallery'): void
    {
        if (!$request->hasFile($field)) {
            return;
        }

        $count = $product->images()->count();

        foreach ($request->file($field) as $i => $img) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $img->store('products', 'public'),
                'sort_order' => $count + $i,
                'is_primary' => $count === 0 && $i === 0,
            ]);
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product-copy';
        $slug = $base;
        $counter = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function uniqueSku(?string $sku): string
    {
        $base = Str::upper(Str::slug($sku ?: 'SG-COPY-' . Str::random(6), '-'));
        $candidate = "{$base}-COPY";
        $counter = 2;

        while (Product::where('sku', $candidate)->exists()) {
            $candidate = "{$base}-COPY-{$counter}";
            $counter++;
        }

        return $candidate;
    }

    private function uniqueVariantSku(string $sku): string
    {
        $base = Str::upper(Str::slug($sku, '-'));
        $candidate = "{$base}-COPY";
        $counter = 2;

        while (\App\Models\ProductVariant::where('sku', $candidate)->exists()) {
            $candidate = "{$base}-COPY-{$counter}";
            $counter++;
        }

        return $candidate;
    }

    private function copyGalleryImages(Product $sourceProduct, Product $product): void
    {
        foreach ($sourceProduct->images as $image) {
            $product->images()->create([
                'image_path' => $this->copyPublicFile($image->image_path),
                'sort_order' => $image->sort_order,
                'is_primary' => $image->is_primary,
            ]);
        }
    }

    private function copyVariants(Product $sourceProduct, Product $product): void
    {
        foreach ($sourceProduct->variants as $variant) {
            $variantCopy = $variant->replicate(['sku']);
            $variantCopy->product_id = $product->id;
            $variantCopy->sku = $variant->sku ? $this->uniqueVariantSku($variant->sku) : null;
            $variantCopy->save();
        }
    }

    private function copyPublicFile(?string $path): ?string
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return $path;
        }

        $directory = trim(pathinfo($path, PATHINFO_DIRNAME), '.');
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $suffix = 'copy-' . Str::random(8);
        $targetName = $extension ? "{$filename}-{$suffix}.{$extension}" : "{$filename}-{$suffix}";
        $targetPath = $directory ? "{$directory}/{$targetName}" : $targetName;

        Storage::disk('public')->copy($path, $targetPath);

        return $targetPath;
    }
}
