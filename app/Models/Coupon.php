<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order_amount', 'max_discount_amount',
        'usage_limit', 'per_user_limit', 'used_count', 'starts_at', 'ends_at', 'status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function usages() { return $this->hasMany(CouponUsage::class); }

    public function isValid(): bool {
        if ($this->status !== 'active') return false;
        $now = now();
        if ($this->starts_at && $now < $this->starts_at) return false;
        if ($this->ends_at && $now > $this->ends_at) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float {
        if ($this->type === 'fixed') {
            $discount = $this->value;
        } else {
            $discount = ($subtotal * $this->value) / 100;
        }
        if ($this->max_discount_amount) {
            $discount = min($discount, $this->max_discount_amount);
        }
        return min($discount, $subtotal);
    }
}
