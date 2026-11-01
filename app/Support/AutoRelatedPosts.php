<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Collection;

class AutoRelatedPosts
{
    public const LIMIT = 3;

    /**
     * Sinkronkan 3 berita "Baca Juga" otomatis.
     * Prioritas: kanal yang sama → berita terbaru terbit.
     *
     * @return list<int>
     */
    public static function syncFor(Post $post, int $limit = self::LIMIT): array
    {
        $ids = self::suggestIdsFor($post, $limit);

        $post->relatedPosts()->sync($ids);

        return $ids;
    }

    /**
     * @return list<int>
     */
    public static function suggestIdsFor(Post $post, int $limit = self::LIMIT): array
    {
        if ($limit < 1) {
            return [];
        }

        $excludeId = $post->getKey();
        $categoryIds = $post->relationLoaded('categories')
            ? $post->categories->pluck('id')
            : $post->categories()->pluck('categories.id');

        $related = collect();

        if ($categoryIds->isNotEmpty()) {
            $related = self::publishedCandidates($excludeId)
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
                ->latest('published_at')
                ->limit($limit)
                ->pluck('id');
        }

        if ($related->count() < $limit) {
            $extra = self::publishedCandidates($excludeId)
                ->when($related->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $related))
                ->latest('published_at')
                ->limit($limit - $related->count())
                ->pluck('id');

            $related = $related->concat($extra)->unique()->values();
        }

        return $related->take($limit)->map(fn ($id) => (int) $id)->all();
    }

    protected static function publishedCandidates(int|string|null $excludeId)
    {
        return Post::query()
            ->published()
            ->when($excludeId, fn ($q) => $q->whereKeyNot($excludeId));
    }

    /**
     * @return Collection<int, Post>
     */
    public static function previewFor(Post $post, int $limit = self::LIMIT): Collection
    {
        if ($post->relationLoaded('relatedPosts') && $post->relatedPosts->isNotEmpty()) {
            return $post->relatedPosts
                ->take($limit)
                ->values();
        }

        $ids = $post->relatedPosts()->exists()
            ? $post->relatedPosts()->pluck('posts.id')->take($limit)->all()
            : self::suggestIdsFor($post, $limit);

        if ($ids === []) {
            return collect();
        }

        return Post::query()
            ->whereIn('id', $ids)
            ->get(['id', 'title', 'slug', 'status'])
            ->sortBy(fn (Post $item) => array_search($item->id, $ids, true))
            ->values();
    }
}
