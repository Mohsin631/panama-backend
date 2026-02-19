<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'email','password',
        'business_name','category_id','location','whatsapp_no','about','years_in_business',
        'export_markets','languages','image_path',
        'onboarding_step','status','rejection_reason',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'export_markets' => 'array',
        'languages' => 'array',
        'email_verified_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function settings()
    {
        return $this->hasOne(\App\Models\VendorSetting::class);
    }
}
