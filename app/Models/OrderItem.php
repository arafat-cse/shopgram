<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'variant_id', 'product_name',
        'variant_info', 'quantity', 'purchase_price', 'selling_price',
        'unit_price', 'total_price',
    ];

    protected $casts = [
        'variant_info' => 'array',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}
