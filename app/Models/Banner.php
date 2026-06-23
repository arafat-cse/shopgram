<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'button_text', 'button_url',
        'image', 'type', 'sort_order', 'status',
    ];

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeHero($query) { return $query->where('type', 'hero'); }
    public function scopePromo($query) { return $query->where('type', 'promo'); }
}
