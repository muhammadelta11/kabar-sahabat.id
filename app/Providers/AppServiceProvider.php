<?php

namespace App\Providers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Observers\NewsCacheObserver;
use App\Support\NewsCache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            View::share('siteSettings', NewsCache::siteSettings());
        }

        View::composer(['news.index', 'news.show', 'pages.*'], function ($view): void {
            $view->with(
                'navCategories',
                Schema::hasTable('categories') ? NewsCache::navCategories() : collect()
            );
        });

        if (Schema::hasTable('posts')) {
            Post::observe(NewsCacheObserver::class);
        }

        if (Schema::hasTable('categories')) {
            Category::observe(NewsCacheObserver::class);
        }

        if (Schema::hasTable('advertisements')) {
            Advertisement::observe(NewsCacheObserver::class);
        }

        if (Schema::hasTable('settings')) {
            Setting::observe(NewsCacheObserver::class);
        }

        if (Schema::hasTable('comments')) {
            Comment::observe(NewsCacheObserver::class);
        }
    }
}
