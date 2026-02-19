<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'plan_name',
        'description',
        'price',
        'currency',
        'validity_value',
        'validity_unit',
        'is_active',
    ];
}
