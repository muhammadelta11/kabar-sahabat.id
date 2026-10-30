<?php

use App\Models\Post;
use App\Models\PostSlugRedirect;
use App\Models\User;
use App\Support\PostSlug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('membuat slug bersih unik dari judul', function () {
    $slug = PostSlug::unique('Relawan Gabungan Perkuat Sekat Api di Gunung Lawu, 325 Personel');

    expect($slug)->toBe('relawan-gabungan-perkuat-sekat-api-di-gunung-lawu-325-personel')
        ->and(PostSlug::isClean($slug))->toBeTrue()
        ->and(PostSlug::isClean('Relawan Gabungan'))->toBeFalse();
});

it('menyimpan redirect 301 saat slug diubah', function () {
    $post = Post::factory()->create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Judul Lama Spasi Banyak Sekali',
        'slug' => 'Judul Lama Spasi Banyak Sekali',
        'status' => 'Published',
        'published_at' => now()->subHour(),
    ]);

    expect($post->fresh()->slug)->toBe('judul-lama-spasi-banyak-sekali');

    get(route('news.show', 'Judul Lama Spasi Banyak Sekali'))
        ->assertRedirect(route('news.show', 'judul-lama-spasi-banyak-sekali'));

    expect(PostSlugRedirect::where('old_slug', 'Judul Lama Spasi Banyak Sekali')->exists())->toBeTrue();
});

it('menormalisasi slug kotor lewat artisan command', function () {
    $post = Post::factory()->create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Pegadaian Tebar Kebaikan Jumat Berkah',
        'slug' => 'already-clean-slug',
        'status' => 'Published',
        'published_at' => now()->subHour(),
    ]);

    // Bypass model hooks: slug kotor langsung di DB
    $post->forceFill(['slug' => 'Pegadaian Tebar Kebaikan, Jumat Berkah'])->saveQuietly();

    Artisan::call('posts:normalize-slugs');

    expect($post->fresh()->slug)->toBe('pegadaian-tebar-kebaikan-jumat-berkah')
        ->and(PostSlugRedirect::where('old_slug', 'Pegadaian Tebar Kebaikan, Jumat Berkah')->value('post_id'))
        ->toBe($post->id);

    get('/berita/'.rawurlencode('Pegadaian Tebar Kebaikan, Jumat Berkah'))
        ->assertRedirect(route('news.show', 'pegadaian-tebar-kebaikan-jumat-berkah'));
});
