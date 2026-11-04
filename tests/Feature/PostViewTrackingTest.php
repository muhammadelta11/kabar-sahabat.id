<?php

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

function createPublishedPost(): Post
{
    $user = User::factory()->create();

    return Post::factory()->create(['status' => 'Published', 'user_id' => $user->id, 'views_count' => 0]);
}

it('mencatat satu post view saat artikel dibaca', function () {
    $post = createPublishedPost();

    get(route('news.show', $post->slug))->assertSuccessful();

    expect(PostView::where('post_id', $post->id)->count())->toBe(1)
        ->and($post->fresh()->views_count)->toBe(1);
});

it('grafik pembaca harian menghitung view hari ini', function () {
    $post = createPublishedPost();

    get(route('news.show', $post->slug))->assertSuccessful();
    get(route('news.show', $post->slug))->assertSuccessful();

    expect(PostView::where('post_id', $post->id)->count())->toBe(2)
        ->and(PostView::query()->where('viewed_at', '>=', now()->startOfDay())->count())->toBe(2);
});
