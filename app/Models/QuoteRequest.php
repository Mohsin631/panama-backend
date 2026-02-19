<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'user_id','vendor_id','product_id','product_title',
        'quantity','unit','shipping_country','shipping_city','note',
        'quoted_price','currency','quoted_moq','status','last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function vendor(){ return $this->belongsTo(Vendor::class); }
    public function product(){ return $this->belongsTo(Product::class); }

    public function messages(){ return $this->hasMany(QuoteMessage::class); }
    public function history(){ return $this->hasMany(QuoteStatusHistory::class); }

    public function addSystemMessage(string $text, string $type = 'system', array $meta = []): \App\Models\QuoteMessage
    {
        return $this->messages()->create([
            'sender_type' => 'system',
            'sender_id' => null,
            'message_type' => $type,
            'message' => $text,
            'meta' => $meta ?: null,
        ]);
    }
}
