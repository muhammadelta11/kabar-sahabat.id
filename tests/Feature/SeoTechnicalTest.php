<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('menandai halaman pencarian sebagai noindex', function () {
    get('/?q=pmii')
        ->assertSuccessful()
        ->assertSee('name="robots" content="noindex, follow"', false);
});

it('menyertakan NewsArticle schema dengan inLanguage pada halaman artikel', function () {
    $post = Post::factory()->create([
        'status' => 'Published',
        'user_id' => User::factory()->create()->id,
        'published_at' => now()->subHour(),
        'image_url' => 'posts/seo-cover.jpg',
        'title' => 'Judul Artikel SEO',
    ]);

    get(route('news.show', $post->slug))
        ->assertSuccessful()
        ->assertSee('"@type": "NewsArticle"', false)
        ->assertSee('"inLanguage": "id-ID"', false)
        ->assertSee('"isAccessibleForFree": true', false)
        ->assertSee(media_absolute_url('posts/seo-cover.jpg'), false);
});

it('sitemap posts memakai image:loc absolut', function () {
    Post::factory()->create([
        'status' => 'Published',
        'user_id' => User::factory()->create()->id,
        'published_at' => now()->subHour(),
        'image_url' => 'posts/sitemap-cover.jpg',
        'title' => 'Berita Sitemap',
    ]);

    $response = get(route('sitemap.posts'));

    $response->assertSuccessful();
    expect($response->headers->get('Content-Type'))->toContain('xml');
    expect($response->getContent())
        ->toContain('<image:loc>'.media_absolute_url('posts/sitemap-cover.jpg').'</image:loc>')
        ->toContain('<news:news>')
        ->toContain('<news:title>Berita Sitemap</news:title>');
});
