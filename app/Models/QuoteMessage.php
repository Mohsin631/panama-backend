<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteMessage extends Model
{
    protected $fillable = [
        'quote_request_id','sender_type','sender_id','message','message_type','meta',
        'attachment_path','attachment_type'
    ];

    protected $casts = [
        'meta' => 'array',
        'seen_by_user_at' => 'datetime',
        'seen_by_vendor_at' => 'datetime',
    ];

    public function quoteRequest(){ return $this->belongsTo(QuoteRequest::class); }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? asset('storage/'.$this->attachment_path) : null;
    }

}
