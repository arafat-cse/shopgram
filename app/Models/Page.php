<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'seo_title',
        'seo_description', 'seo_keywords', 'status',
    ];

    public function scopeActive($query) { return $query->where('status', 'active'); }
}
