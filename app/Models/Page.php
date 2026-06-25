<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'seo_title',
        'seo_description', 'seo_keywords', 'status', 'show_in_footer',
    ];

    protected $casts = ['show_in_footer' => 'boolean'];

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeFooter($query) { return $query->where('show_in_footer', true); }
}
