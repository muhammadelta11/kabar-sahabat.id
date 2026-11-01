<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class EditorialInsights
{
    /**
     * @return Collection<int, Post>
     */
    public static function topPostsAllTime(int $limit = 10): Collection
    {
        $key = 'admin.editorial.top_posts_all.'.$limit;
        $ids = Cache::get($key);

        if (! is_array($ids)) {
            Cache::forget($key);
            $ids = Cache::remember($key, 60, function () use ($limit): array {
                return Post::query()
                    ->published()
                    ->orderByDesc('views_count')
                    ->limit($limit)
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
                    ->all();
            });
        }

        if ($ids === []) {
            return collect();
        }

        return Post::query()
            ->published()
            ->with(['categories:id,name,slug', 'user:id,name'])
            ->select(['id', 'title', 'slug', 'views_count', 'published_at', 'user_id', 'status'])
            ->whereIn('id', $ids)
            ->orderByDesc('views_count')
            ->get();
    }

    /**
     * @return Collection<int, object{post_id: int, total: int, title: string, slug: ?string, views_count: int}>
     */
    public static function topPostsLast7Days(int $limit = 10): Collection
    {
        $key = 'admin.editorial.top_posts_7d.'.$limit;
        $rows = Cache::get($key);

        if (! is_array($rows)) {
            Cache::forget($key);
            $rows = Cache::remember($key, 60, function () use ($limit): array {
                $start = now()->subDays(6)->startOfDay();

                $viewRows = PostView::query()
                    ->select('post_id', DB::raw('COUNT(*) as total'))
                    ->where('viewed_at', '>=', $start)
                    ->groupBy('post_id')
                    ->orderByDesc('total')
                    ->limit($limit)
                    ->get();

                if ($viewRows->isEmpty()) {
                    return [];
                }

                $posts = Post::query()
                    ->whereIn('id', $viewRows->pluck('post_id'))
                    ->get(['id', 'title', 'slug', 'views_count', 'published_at'])
                    ->keyBy('id');

                return $viewRows->map(function ($row) use ($posts): array {
                    $post = $posts->get($row->post_id);

                    return [
                        'post_id' => (int) $row->post_id,
                        'total' => (int) $row->total,
                        'title' => $post?->title ?? 'Berita dihapus',
                        'slug' => $post?->slug,
                        'views_count' => (int) ($post?->views_count ?? 0),
                    ];
                })->all();
            });
        }

        return collect($rows)->map(fn (array $row) => (object) $row);
    }

    /**
     * @return Collection<int, object{name: string, slug: string, total: int}>
     */
    public static function topCategoriesLast7Days(int $limit = 5): Collection
    {
        $key = 'admin.editorial.top_categories_7d.'.$limit;
        $rows = Cache::get($key);

        if (! is_array($rows)) {
            Cache::forget($key);
            $rows = Cache::remember($key, 60, function () use ($limit): array {
                $start = now()->subDays(6)->startOfDay();

                return Category::query()
                    ->select([
                        'categories.id',
                        'categories.name',
                        'categories.slug',
                        DB::raw('COUNT(post_views.id) as total'),
                    ])
                    ->join('category_post', 'category_post.category_id', '=', 'categories.id')
                    ->join('post_views', 'post_views.post_id', '=', 'category_post.post_id')
                    ->where('post_views.viewed_at', '>=', $start)
                    ->groupBy('categories.id', 'categories.name', 'categories.slug')
                    ->orderByDesc('total')
                    ->limit($limit)
                    ->get()
                    ->map(fn (Category $category): array => [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'total' => (int) $category->total,
                    ])
                    ->all();
            });
        }

        return collect($rows)->map(fn (array $row) => (object) $row);
    }

    public static function forgetCache(): void
    {
        foreach ([10, 5] as $limit) {
            Cache::forget('admin.editorial.top_posts_all.'.$limit);
            Cache::forget('admin.editorial.top_posts_7d.'.$limit);
            Cache::forget('admin.editorial.top_categories_7d.'.$limit);
        }
    }
}
