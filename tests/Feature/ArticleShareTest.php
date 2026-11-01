<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
});

test('halaman detail menampilkan tombol bagikan sosial', function () {
    $post = Post::factory()->create([
        'status' => 'Published',
        'user_id' => User::factory()->create()->id,
        'published_at' => now()->subHour(),
        'title' => 'Berita Uji Share',
    ]);

    $html = get(route('news.show', $post->slug))->assertSuccessful()->getContent();
    $shareUrl = route('news.show', $post->slug);

    expect($html)
        ->toContain('Bagikan')
        ->toContain('api.whatsapp.com/send?text='.rawurlencode($shareUrl))
        ->toContain('facebook.com/sharer/sharer.php')
        ->toContain('twitter.com/intent/tweet')
        ->toContain('id="copy-link-btn"')
        ->toContain('id="native-share-btn"')
        ->not->toContain('api.whatsapp.com/send?text='.rawurlencode('Berita Uji Share '.$shareUrl));
});

test('meta open graph artikel memakai judul dan gambar absolut', function () {
    $post = Post::factory()->create([
        'status' => 'Published',
        'user_id' => User::factory()->create()->id,
        'published_at' => now()->subHour(),
        'title' => 'Judul Preview Share',
        'image_url' => 'posts/share-cover.jpg',
    ]);

    $html = get(route('news.show', $post->slug))->assertSuccessful()->getContent();
    $image = media_absolute_url('posts/share-cover.jpg');

    expect($html)
        ->toContain('property="og:title" content="Judul Preview Share"')
        ->toContain('property="og:image" content="'.$image.'"')
        ->toContain('name="twitter:card" content="summary_large_image"')
        ->toContain('name="twitter:image" content="'.$image.'"');
});

test('meta open graph beranda memakai logo brand bukan gambar berita', function () {
    $author = User::factory()->create();
    Post::factory()->create([
        'status' => 'Published',
        'user_id' => $author->id,
        'published_at' => now()->subHour(),
        'image_url' => 'posts/random-news.jpg',
    ]);

    $html = get('/')->assertSuccessful()->getContent();

    preg_match('/property="og:image" content="([^"]+)"/', $html, $og);
    preg_match('/name="twitter:image" content="([^"]+)"/', $html, $tw);

    expect($html)->toContain('name="twitter:card" content="summary"')
        ->and($og[1] ?? null)->not->toBeNull()
        ->and($og[1])->not->toContain('posts/random-news.jpg')
        ->and($tw[1] ?? null)->toBe($og[1])
        ->and(
            str_contains($og[1], 'images/brand/logo-dark.png')
            || str_contains($og[1], 'settings/')
        )->toBeTrue();
});
