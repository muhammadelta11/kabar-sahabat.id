<?php

namespace App\Support;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NewsCache
{
    public const TTL_SECONDS = 300;

    public static function flush(): void
    {
        foreach ([
            'news.site_settings',
            'news.nav_categories',
            'news.active_ads',
            'news.latest_posts',
            'news.trending_posts',
            'news.popular_posts',
            'news.popular_posts_sidebar',
            'news.home_sections',
            'admin.stats_overview',
            'admin.views_chart_7d',
        ] as $key) {
            Cache::forget($key);
        }
    }

    public static function siteSettings(): Setting
    {
        $id = self::rememberScalar('news.site_settings', function () {
            $setting = Setting::query()->first() ?? Setting::query()->create([
                'tagline' => 'Jernih Melihat Pergerakan',
                'copyright' => 'Copyright &copy; '.now()->format('Y').' Kabar Sahabat. All Rights Reserved',
            ]);

            return (int) $setting->id;
        });

        return Setting::query()->findOrFail($id);
    }

    public static function navCategories(): Collection
    {
        $ids = self::rememberIdList('news.nav_categories', function () {
            return Category::query()->orderBy('name')->pluck('id')->all();
        });

        if ($ids === []) {
            return collect();
        }

        return Category::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    public static function activeAds(): Collection
    {
        $ids = self::rememberIdList('news.active_ads', function () {
            return Advertisement::query()->where('is_active', true)->pluck('id')->all();
        });

        if ($ids === []) {
            return collect();
        }

        return Advertisement::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('slot_key');
    }

    public static function latestPosts(int $limit = 12): Collection
    {
        return self::cachedPostCollection('news.latest_posts', $limit, function ($query) {
            $query->latest('published_at');
        });
    }

    public static function trendingPosts(int $limit = 10): Collection
    {
        return self::cachedPostCollection('news.trending_posts', $limit, function ($query) {
            $query->orderByDesc('views_count');
        });
    }

    public static function popularPosts(int $limit = 7, string $cacheKey = 'news.popular_posts'): Collection
    {
        return self::cachedPostCollection($cacheKey, $limit, function ($query) {
            $query->orderByDesc('views_count');
        });
    }

    public static function homeSections(): Collection
    {
        $categoryIds = self::rememberIdList('news.home_sections', function () {
            return Category::query()
                ->where('show_on_homepage', true)
                ->orderBy('name')
                ->pluck('id')
                ->all();
        });

        if ($categoryIds === []) {
            return collect();
        }

        return Category::query()
            ->whereIn('id', $categoryIds)
            ->orderBy('name')
            ->get()
            ->map(function (Category $category) {
                $posts = $category->posts()
                    ->with(['categories:id,name,slug'])
                    ->published()
                    ->latest('published_at')
                    ->limit(4)
                    ->get();

                $category->setRelation('homepagePosts', $posts);

                return $category;
            })
            ->filter(fn (Category $category) => $category->homepagePosts->isNotEmpty())
            ->values();
    }

    /**
     * @param  callable(Builder): void  $configure
     */
    protected static function cachedPostCollection(string $cacheKey, int $limit, callable $configure): Collection
    {
        $ids = self::rememberIdList($cacheKey, function () use ($limit, $configure) {
            $query = Post::query()->published()->select('id');
            $configure($query);

            return $query->limit($limit)->pluck('id')->all();
        });

        if ($ids === []) {
            return collect();
        }

        $posts = Post::query()
            ->with(['categories:id,name,slug'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn ($id) => $posts->get($id))
            ->filter()
            ->values();
    }

    /**
     * @param  callable(): int  $callback
     */
    protected static function rememberScalar(string $key, callable $callback): int
    {
        $cached = Cache::get($key);

        if (is_int($cached) || (is_string($cached) && ctype_digit($cached))) {
            return (int) $cached;
        }

        if ($cached !== null) {
            Cache::forget($key);
        }

        $value = (int) $callback();
        Cache::put($key, $value, self::TTL_SECONDS);

        return $value;
    }

    /**
     * @param  callable(): array<int, int|string>  $callback
     * @return array<int, int>
     */
    protected static function rememberIdList(string $key, callable $callback): array
    {
        $cached = Cache::get($key);

        if (is_array($cached) && self::isListOfScalars($cached)) {
            return array_map('intval', $cached);
        }

        if ($cached !== null) {
            Cache::forget($key);
        }

        $value = array_map('intval', $callback());
        Cache::put($key, $value, self::TTL_SECONDS);

        return $value;
    }

    /**
     * @param  array<mixed>  $values
     */
    protected static function isListOfScalars(array $values): bool
    {
        foreach ($values as $value) {
            if (! is_int($value) && ! (is_string($value) && ctype_digit($value))) {
                return false;
            }
        }

        return true;
    }
}
