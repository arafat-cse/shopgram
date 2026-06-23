<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'coupon_id', 'shipping_zone_id', 'courier_id',
        'billing_address', 'shipping_address', 'subtotal', 'discount_amount',
        'shipping_charge', 'tax_amount', 'total', 'payment_method', 'payment_status',
        'delivery_method', 'courier_tracking_number', 'order_note', 'status',
        'placed_at', 'estimated_delivery_date',
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'placed_at' => 'datetime',
        'estimated_delivery_date' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function shippingZone() { return $this->belongsTo(ShippingZone::class); }
    public function courier() { return $this->belongsTo(Courier::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function statusHistories() { return $this->hasMany(OrderStatusHistory::class)->latest(); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function returnRequests() { return $this->hasMany(ReturnRequest::class); }

    public static function generateOrderNumber(): string {
        return 'SG-' . strtoupper(uniqid());
    }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'packed' => 'Packed',
            'shipped' => 'Shipped',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned',
            'refunded' => 'Refunded',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'pending' => 'secondary',
            'confirmed' => 'info',
            'processing' => 'primary',
            'packed' => 'warning',
            'shipped' => 'purple',
            'out_for_delivery' => 'orange',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'returned' => 'dark',
            'refunded' => 'teal',
            default => 'secondary',
        };
    }
}
