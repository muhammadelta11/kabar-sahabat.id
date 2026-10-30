<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecordPostView implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [1, 5, 15];

    public int $timeout = 30;

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
    }

    public function failed(?Throwable $exception): void
    {
        Log::warning('Gagal mencatat view artikel', [
            'post_id' => $this->postId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
