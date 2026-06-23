<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
