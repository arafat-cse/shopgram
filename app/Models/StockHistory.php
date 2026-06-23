<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = [
        'product_id', 'variant_id', 'type', 'quantity', 'note', 'created_by',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
