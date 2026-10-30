<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
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
            $setting = Setting::first() ?? Setting::create([
                'tagline' => 'Jernih Melihat Pergerakan',
                'copyright' => 'Copyright &copy; '.now()->format('Y').' Kabar Sahabat. All Rights Reserved',
            ]);
            View::share('siteSettings', $setting);
        }

        View::composer(['news.index', 'news.show', 'pages.*'], function ($view): void {
            $view->with('navCategories', Schema::hasTable('categories')
                ? Category::query()->orderBy('name')->get()
                : collect());
        });
    }
}
