<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo_path',
        'tagline',
        'copyright',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'facebook_url',
        'threads_url',
        'tiktok_url',
        'email',
        'phone',
        'whatsapp_enabled',
        'whatsapp_number',
        'whatsapp_message',
        'address',
        'analytics_code',
    ];
}
