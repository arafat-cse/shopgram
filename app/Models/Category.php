<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'image', 'icon', 'status',
        'seo_title', 'seo_description', 'seo_keywords',
    ];

    public function parent() { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children() { return $this->hasMany(Category::class, 'parent_id'); }
    public function products() { return $this->hasMany(Product::class); }

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeParent($query) { return $query->whereNull('parent_id'); }
}
