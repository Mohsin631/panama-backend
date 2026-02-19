<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'vendor_id','category_id','title','short_description','description',
        'location','currency','price','old_price','moq',
        'is_deal','is_active','status','ideal_for','tags'
    ];

    protected $casts = [
        'ideal_for' => 'array',
        'tags' => 'array',
        'is_deal' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function category() { return $this->belongsTo(Category::class); }

    public function media() { return $this->hasMany(ProductMedia::class); }
    public function images() { return $this->hasMany(ProductMedia::class)->where('type','image')->orderBy('sort_order'); }
    public function videos() { return $this->hasMany(ProductMedia::class)->where('type','video')->orderBy('sort_order'); }
}
