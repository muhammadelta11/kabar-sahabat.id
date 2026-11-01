<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'slot_key',
        'name',
        'client_name',
        'type',
        'image_url',
        'link_url',
        'script_code',
        'is_active',
        'monthly_rate',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'monthly_rate' => 'integer',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];
}
