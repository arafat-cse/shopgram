<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = ['name', 'phone', 'tracking_url', 'status'];

    public function orders() { return $this->hasMany(Order::class); }
    public function scopeActive($query) { return $query->where('status', 'active'); }

    public function getTrackingLink(string $trackingNumber): ?string {
        if ($this->tracking_url && $trackingNumber) {
            return str_replace('{tracking}', $trackingNumber, $this->tracking_url);
        }
        return null;
    }
}
