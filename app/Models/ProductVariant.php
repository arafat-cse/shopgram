<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'sku', 'size', 'color', 'weight',
        'material', 'custom_option', 'price', 'stock_quantity',
    ];

    protected $casts = ['price' => 'decimal:2'];

    public function product() { return $this->belongsTo(Product::class); }

    public function getDisplayNameAttribute(): string {
        $parts = array_filter([
            $this->size ? "Size: {$this->size}" : null,
            $this->color ? "Color: {$this->color}" : null,
            $this->weight ? "Weight: {$this->weight}" : null,
            $this->custom_option ?: null,
        ]);
        return implode(', ', $parts) ?: 'Default';
    }

    public static function generateSku(Product $product, array $variantData, ?int $ignoreVariantId = null): string
    {
        $base = collect([
            $product->category?->name,
            $product->name,
            $variantData['size'] ?? null,
            $variantData['color'] ?? null,
            $variantData['custom_option'] ?? null,
        ])->filter()->map(fn ($part) => Str::upper(Str::slug($part, '-')))->implode('-');

        $sku = $base;
        $suffix = 1;
        while (
            static::where('sku', $sku)
                ->when($ignoreVariantId, fn ($q) => $q->where('id', '!=', $ignoreVariantId))
                ->exists()
        ) {
            $sku = $base.'-'.(++$suffix);
        }

        return $sku;
    }
}
