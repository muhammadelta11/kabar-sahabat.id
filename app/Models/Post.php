<?php

namespace App\Models;

use App\Support\Media;
use App\Support\PostSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /** @var array<int, string> */
    protected static array $pendingSlugRedirects = [];

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            $incoming = filled($post->slug) ? (string) $post->slug : (string) $post->title;
            $clean = PostSlug::unique($incoming, $post->exists ? $post->id : null);

            if ($post->exists) {
                $old = (string) $post->getOriginal('slug');
                if ($old !== '' && $old !== $clean) {
                    static::$pendingSlugRedirects[spl_object_id($post)] = $old;
                }
            } elseif ($incoming !== '' && $incoming !== $clean) {
                static::$pendingSlugRedirects[spl_object_id($post)] = $incoming;
            }

            $post->slug = $clean;
        });

        static::saved(function (Post $post): void {
            $objectId = spl_object_id($post);

            if (! isset(static::$pendingSlugRedirects[$objectId])) {
                return;
            }

            $oldSlug = static::$pendingSlugRedirects[$objectId];
            unset(static::$pendingSlugRedirects[$objectId]);

            PostSlugRedirect::query()->where('old_slug', $post->slug)->delete();

            PostSlugRedirect::query()->updateOrCreate(
                ['old_slug' => $oldSlug],
                ['post_id' => $post->id]
            );
        });

        static::deleted(function (Post $post): void {
            preg_match_all('#storage/kiriman/([A-Za-z0-9]+\.(?:jpe?g|png|webp))#i', (string) $post->content, $matches);

            foreach (array_unique($matches[1]) as $filename) {
                Media::delete('kiriman/'.$filename);
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
