<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'sku',
        'short_description', 'description', 'specification',
        'regular_price', 'sale_price', 'purchase_price',
        'stock_quantity', 'low_stock_threshold', 'thumbnail',
        'status', 'is_featured', 'is_new_arrival', 'is_best_selling',
        'seo_title', 'seo_description', 'seo_keywords',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_best_selling' => 'boolean',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function images() { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function primaryImage() { return $this->hasOne(ProductImage::class)->where('is_primary', true); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function cartItems() { return $this->hasMany(CartItem::class); }
    public function wishlistItems() { return $this->hasMany(Wishlist::class); }
    public function reviews() { return $this->hasMany(Review::class)->where('status', 'approved'); }
    public function allReviews() { return $this->hasMany(Review::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function stockHistories() { return $this->hasMany(StockHistory::class); }

    public function getCurrentPriceAttribute(): float {
        return $this->sale_price ?? $this->regular_price;
    }

    public function getDiscountPercentAttribute(): int {
        if ($this->sale_price && $this->regular_price > 0) {
            return (int) round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
        }
        return 0;
    }

    public function isInStock(): bool { return $this->stock_quantity > 0; }
    public function isLowStock(): bool { return $this->stock_quantity > 0 && $this->stock_quantity <= $this->low_stock_threshold; }

    public function getAverageRatingAttribute(): float {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
    public function scopeNewArrivals($query) { return $query->where('is_new_arrival', true); }
    public function scopeBestSelling($query) { return $query->where('is_best_selling', true); }
}
