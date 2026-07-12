<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'variants'                      => 'required|array',
            'variants.*.size'               => 'nullable|string',
            'variants.*.color'              => 'nullable|string',
            'variants.*.weight'             => 'nullable|string',
            'variants.*.material'           => 'nullable|string',
            'variants.*.custom_option'      => 'nullable|string',
            'variants.*.price'              => 'nullable|numeric|min:0',
            'variants.*.stock_quantity'     => 'required|integer|min:0',
        ]);

        foreach ($data['variants'] as $variantData) {
            if (empty($variantData['size']) && empty($variantData['color']) && empty($variantData['custom_option'])) {
                continue;
            }
            $variantData['sku'] = ProductVariant::generateSku($product, $variantData);
            $product->variants()->create($variantData);
        }

        return back()->with('success', 'Variants saved.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $data = $request->validate([
            'size'           => 'nullable|string',
            'color'          => 'nullable|string',
            'weight'         => 'nullable|string',
            'material'       => 'nullable|string',
            'custom_option'  => 'nullable|string',
            'price'          => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $data['sku'] = ProductVariant::generateSku($product, $data, $variant->id);
        $variant->update($data);
        return back()->with('success', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Variant deleted.');
    }

    public function index(Product $product) { return redirect()->route('admin.products.edit', $product); }
    public function create(Product $product) { return redirect()->route('admin.products.edit', $product); }
    public function show(Product $product, ProductVariant $variant) { return redirect()->route('admin.products.edit', $product); }
    public function edit(Product $product, ProductVariant $variant) { return redirect()->route('admin.products.edit', $product); }
}
