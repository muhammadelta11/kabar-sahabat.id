<?php

use App\Jobs\RecordPostView;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('mencatat view artikel secara sync tanpa mengandalkan queue worker', function () {
    $post = Post::factory()->create([
        'status' => 'Published',
        'user_id' => User::factory()->create()->id,
        'views_count' => 0,
    ]);

    Cache::put('admin.stats_overview', ['today' => 0], 60);

    get(route('news.show', $post->slug))->assertSuccessful();

    expect(PostView::where('post_id', $post->id)->count())->toBe(1)
        ->and($post->fresh()->views_count)->toBe(1)
        ->and(Cache::has('admin.stats_overview'))->toBeFalse();
});

it('menambah views_count dan baris post_views', function () {
    $post = Post::factory()->create([
        'status' => 'Published',
        'user_id' => User::factory()->create()->id,
        'views_count' => 0,
    ]);

    (new RecordPostView($post->id))->handle();

    expect(PostView::where('post_id', $post->id)->count())->toBe(1)
        ->and($post->fresh()->views_count)->toBe(1);
});

it('abaikan post yang sudah dihapus', function () {
    (new RecordPostView(999_999))->handle();

    expect(PostView::count())->toBe(0);
});
