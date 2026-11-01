<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Support\AutoRelatedPosts;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('memilih otomatis 3 berita terkait prioritas kanal sama', function () {
    $author = User::factory()->create();
    $kanal = Category::query()->create([
        'name' => 'Politik Uji',
        'slug' => 'politik-uji-'.uniqid(),
    ]);
    $other = Category::query()->create([
        'name' => 'Ekonomi Uji',
        'slug' => 'ekonomi-uji-'.uniqid(),
    ]);

    $same = Post::factory()->count(3)->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now()->subDay(),
    ]);
    $same->each(fn (Post $p) => $p->categories()->attach($kanal->id));

    Post::factory()->count(2)->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now()->subHours(2),
    ])->each(fn (Post $p) => $p->categories()->attach($other->id));

    $target = Post::factory()->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now(),
        'title' => 'Artikel Target Related',
    ]);
    $target->categories()->attach($kanal->id);

    $ids = AutoRelatedPosts::syncFor($target);

    expect($ids)->toHaveCount(3)
        ->and($ids)->each->toBeIn($same->pluck('id')->all())
        ->and($target->relatedPosts()->count())->toBe(3);
});

it('mengisi sisa related dari berita terbaru jika kanal kurang', function () {
    $author = User::factory()->create();

    $published = Post::factory()->count(2)->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now()->subDay(),
    ]);

    $target = Post::factory()->create([
        'user_id' => $author->id,
        'status' => 'Draft',
        'title' => 'Draft Target',
    ]);

    $ids = AutoRelatedPosts::syncFor($target);

    expect($ids)->toHaveCount(2)
        ->and(collect($ids)->sort()->values()->all())
        ->toEqual($published->pluck('id')->sort()->values()->all());
});
