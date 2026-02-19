<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteStatusHistory extends Model
{
    protected $fillable = [
        'quote_request_id','from_status','to_status','changed_by_type','changed_by_id','note'
    ];

    public function quoteRequest(){ return $this->belongsTo(QuoteRequest::class); }
}
