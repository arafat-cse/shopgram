<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = ['name', 'charge', 'free_above', 'status'];
    protected $casts = ['charge' => 'decimal:2', 'free_above' => 'decimal:2'];

    public function orders() { return $this->hasMany(Order::class); }
    public function scopeActive($query) { return $query->where('status', 'active'); }

    public function calculateCharge(float $subtotal): float {
        if ($this->free_above && $subtotal >= $this->free_above) {
            return 0;
        }
        return $this->charge;
    }
}
