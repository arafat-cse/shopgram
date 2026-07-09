<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinTransaction extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'type', 'amount', 'balance_after', 'description',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }

    public function getTypeLabelAttribute(): string {
        return match($this->type) {
            'earn' => 'Earned',
            'redeem' => 'Redeemed',
            'reversal_earn' => 'Earning Reversed',
            'reversal_redeem' => 'Redemption Refunded',
            'admin_adjust' => 'Admin Adjustment',
            default => ucfirst($this->type),
        };
    }
}
