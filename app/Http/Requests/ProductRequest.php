<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name'                => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255|unique:products,slug,' . $productId,
            'category_id'         => 'required|exists:categories,id',
            'brand_id'            => 'nullable|exists:brands,id',
            'sku'                 => 'nullable|string|max:100|unique:products,sku,' . $productId,
            'short_description'   => 'nullable|string|max:500',
            'description'         => 'nullable|string',
            'specification'       => 'nullable|string',
            'regular_price'       => 'required|numeric|min:0',
            'sale_price'          => 'nullable|numeric|min:0|lt:regular_price',
            'purchase_price'      => 'nullable|numeric|min:0',
            'stock_quantity'      => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'thumbnail'           => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'gallery.*'           => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'status'              => 'required|in:active,inactive',
            'is_featured'         => 'boolean',
            'is_new_arrival'      => 'boolean',
            'is_best_selling'     => 'boolean',
            'seo_title'           => 'nullable|string|max:255',
            'seo_description'     => 'nullable|string|max:500',
            'seo_keywords'        => 'nullable|string|max:500',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured'    => $this->boolean('is_featured'),
            'is_new_arrival' => $this->boolean('is_new_arrival'),
            'is_best_selling'=> $this->boolean('is_best_selling'),
        ]);
    }
}
