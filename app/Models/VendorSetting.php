<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorSetting extends Model
{
    protected $fillable = [
        'vendor_id',
        'new_order_received',
        'order_status_updates',
        'order_cancelled',
        'new_customer_message',
        'admin_messages',
    ];

    protected $casts = [
        'new_order_received' => 'boolean',
        'order_status_updates' => 'boolean',
        'order_cancelled' => 'boolean',
        'new_customer_message' => 'boolean',
        'admin_messages' => 'boolean',
    ];
}
