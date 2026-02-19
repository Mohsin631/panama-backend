<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id','plan_id',
        'stripe_customer_id','stripe_subscription_id','stripe_checkout_session_id',
        'status','starts_at','expires_at','current_period_end','canceled_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function plan() { return $this->belongsTo(Plan::class); }
    public function user() { return $this->belongsTo(User::class); }
}
