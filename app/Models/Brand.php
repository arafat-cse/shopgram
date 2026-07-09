<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'logo', 'status'];

    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return asset('images/no-image.png');
        }
        if (str_starts_with($this->logo, 'images/')) {
            return asset($this->logo);
        }
        return asset('storage/' . $this->logo);
    }

    public function products() { return $this->hasMany(Product::class); }
    public function scopeActive($query) { return $query->where('status', 'active'); }
}
