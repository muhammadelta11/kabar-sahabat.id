<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'penulis',
        'editor',
        'author_name',
        'author_wa',
        'author_instansi',
        'title',
        'slug',
        'image_url',
        'image_caption',
        'excerpt',
        'content',
        'status',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleted(function (Post $post): void {
            preg_match_all('#storage/kiriman/([A-Za-z0-9]+\.(?:jpe?g|png|webp))#i', (string) $post->content, $matches);

            foreach (array_unique($matches[1]) as $filename) {
                Storage::disk('public')->delete('kiriman/'.$filename);
            }
        });
    }

    /**
     * Berita yang sudah terbit dan siap ditampilkan ke publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'Published')
            ->where('published_at', '<=', now());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function relatedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_related', 'post_id', 'related_post_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }
}
