<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Str;

class PostSlug
{
    /**
     * Slug SEO-friendly unik dari judul atau teks mentah.
     */
    public static function unique(string $source, ?int $ignorePostId = null): string
    {
        $base = Str::slug(Str::limit(trim($source), 90, ''));

        if ($base === '') {
            $base = 'berita';
        }

        $slug = $base;
        $suffix = 2;

        while (self::exists($slug, $ignorePostId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * Apakah string sudah berbentuk slug bersih (tanpa spasi/kapital aneh)?
     */
    public static function isClean(?string $slug): bool
    {
        if (blank($slug)) {
            return false;
        }

        return $slug === Str::slug($slug);
    }

    private static function exists(string $slug, ?int $ignorePostId): bool
    {
        return Post::query()
            ->where('slug', $slug)
            ->when($ignorePostId, fn ($query) => $query->whereKeyNot($ignorePostId))
            ->exists();
    }
}
