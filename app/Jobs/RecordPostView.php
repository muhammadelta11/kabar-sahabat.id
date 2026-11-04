<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Catat tayangan artikel tanpa mengandalkan queue worker (dispatchSync).
 */
class RecordPostView
{
    use Queueable;

    public function __construct(public int $postId) {}

    public function handle(): void
    {
        if (! Post::query()->whereKey($this->postId)->exists()) {
            return;
        }

        Post::query()->whereKey($this->postId)->increment('views_count');

        PostView::query()->create([
            'post_id' => $this->postId,
            'viewed_at' => now(),
        ]);

        Cache::forget('admin.stats_overview');
        Cache::forget('admin.views_chart_7d');
        Cache::forget('admin.business_stats.v2');
        Cache::forget('admin.popular_posts_top5');
    }

    public function failed(?Throwable $exception): void
    {
        Log::warning('Gagal mencatat view artikel', [
            'post_id' => $this->postId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
