<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'show_on_homepage'];

    protected $casts = [
        'show_on_homepage' => 'boolean',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
