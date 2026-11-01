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

test('beranda meletakkan branding di atas navigasi dan merender hero slider', function () {
    $author = User::factory()->create();

    Post::factory()->count(20)->create([
        'status' => 'Published',
        'user_id' => $author->id,
        'published_at' => now()->subHour(),
    ]);

    $html = get('/')->assertSuccessful()->getContent();

    $brandingPos = strpos($html, 'data-site-branding');
    $navPos = strpos($html, 'id="main-nav"');
    $heroPos = strpos($html, 'id="hero-slider"');

    expect($brandingPos)->not->toBeFalse()
        ->and($navPos)->not->toBeFalse()
        ->and($heroPos)->not->toBeFalse()
        ->and($brandingPos)->toBeLessThan($navPos)
        ->and($html)->toContain('Berita Terbaru')
        ->and($html)->toContain('Berita Populer')
        ->and($html)->toContain('Newsletter')
        ->and($html)->toContain('E-Paper');
});

test('beranda page 1 menampilkan feed berita lainnya lebih panjang lalu lanjut page 2', function () {
    $author = User::factory()->create();

    Post::factory()->count(45)->create([
        'status' => 'Published',
        'user_id' => $author->id,
        'published_at' => now()->subHour(),
    ]);

    $page1 = get('/')->assertSuccessful();
    $page1->assertSee('Berita Lainnya', false);
    $page1->assertSee('Halaman 1 dari', false);

    $page2 = get('/?page=2')->assertSuccessful();
    $page2->assertSee('Berita Lainnya', false);
    $page2->assertDontSee('id="hero-slider"', false);
});

test('nav publik full-bleed di mobile class dan tetap punya batas desktop', function () {
    $html = get('/')->assertSuccessful()->getContent();

    expect($html)
        ->toContain('id="main-nav-container"')
        ->toContain('md:max-w-[var(--site-max)]')
        ->toContain('px-0');
});

test('halaman detail menampilkan sidebar sticky yang lebih lengkap', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create([
        'status' => 'Published',
        'user_id' => $author->id,
        'published_at' => now()->subHour(),
        'title' => 'Artikel Detail Sidebar',
    ]);

    Post::factory()->count(5)->create([
        'status' => 'Published',
        'user_id' => $author->id,
        'published_at' => now()->subHours(2),
    ]);

    get(route('news.show', $post->slug))
        ->assertSuccessful()
        ->assertSee('Berita Populer', false)
        ->assertSee('Berita Terkini', false)
        ->assertSee('Newsletter', false)
        ->assertSee('lg:sticky', false);
});
