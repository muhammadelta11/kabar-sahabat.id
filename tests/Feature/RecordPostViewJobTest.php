<?php

use App\Jobs\RecordPostView;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('mengantri RecordPostView saat artikel dibuka', function () {
    Queue::fake();

    $post = Post::factory()->create([
        'status' => 'Published',
        'user_id' => User::factory()->create()->id,
        'views_count' => 0,
    ]);

    get(route('news.show', $post->slug))->assertSuccessful();

    Queue::assertPushed(RecordPostView::class, fn (RecordPostView $job) => $job->postId === $post->id);
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
