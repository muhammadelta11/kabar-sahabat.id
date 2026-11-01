<?php

use App\Models\Post;
use App\Models\User;
use App\Support\NewsCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
});

it('menampilkan strip beranda lebih dari dua kartu saat stok cukup', function () {
    $author = User::factory()->create();

    Post::factory()->count(24)->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now()->subHour(),
    ]);

    $html = get('/')->assertSuccessful()->getContent();

    expect(substr_count($html, 'id="berita-strip-slider"'))->toBe(1);

    // Kartu strip memakai class card-sm width marker
    expect(substr_count($html, 'snap-start'))->toBeGreaterThanOrEqual(8);
});

it('meng-cache latest posts per limit agar strip tidak kehabisan stok', function () {
    $author = User::factory()->create();

    Post::factory()->count(20)->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now()->subHour(),
    ]);

    expect(NewsCache::latestPosts(12))->toHaveCount(12)
        ->and(NewsCache::latestPosts(24))->toHaveCount(20);
});

it('menampilkan indikator login di topbar untuk kontributor dan admin', function () {
    $kontributor = User::factory()->kontributor()->create(['name' => 'Budi Santoso']);

    actingAs($kontributor)
        ->get('/')
        ->assertSuccessful()
        ->assertSee('Budi', false)
        ->assertSee('Keluar', false)
        ->assertSee(route('account.edit'), false);

    $redaktur = User::factory()->redaktur()->create();

    actingAs($redaktur)
        ->get('/')
        ->assertSuccessful()
        ->assertSee('Admin', false)
        ->assertSee('/admin', false)
        ->assertSee('Keluar', false);
});
