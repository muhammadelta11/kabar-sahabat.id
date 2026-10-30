<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'slot_key',
        'name',
        'type',
        'image_url',
        'link_url',
        'script_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
