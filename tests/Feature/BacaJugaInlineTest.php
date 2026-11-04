<?php

use App\Models\Post;
use App\Models\User;
use App\Support\AutoRelatedPosts;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('menyiapkan minimal 3 baca juga dan skrip sebar rata di halaman artikel', function () {
    $author = User::factory()->create();

    $related = Post::factory()->count(3)->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now()->subDay(),
    ]);

    $post = Post::factory()->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now(),
        'title' => 'Artikel Baca Juga Inline',
        'content' => '<p>Paragraf satu.</p><p>Paragraf dua.</p><p>Paragraf tiga.</p><p>Paragraf empat.</p><p>Paragraf lima.</p><p>Paragraf enam.</p>',
    ]);

    AutoRelatedPosts::syncFor($post);

    $html = get(route('news.show', $post->slug))
        ->assertSuccessful()
        ->getContent();

    expect(substr_count($html, 'baca-juga-item'))->toBeGreaterThanOrEqual(AutoRelatedPosts::LIMIT)
        ->and($html)->toContain('Baca juga')
        ->and($html)->toContain('paragraphs.length / (count + 1)')
        ->and($related->every(fn (Post $item) => str_contains($html, $item->title)))->toBeTrue();
});

it('tetap mengisi sampai 3 baca juga meski related tersimpan kurang', function () {
    $author = User::factory()->create();

    Post::factory()->count(4)->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now()->subDays(2),
    ]);

    $onlyTwo = Post::factory()->count(2)->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now()->subDay(),
    ]);

    $post = Post::factory()->create([
        'user_id' => $author->id,
        'status' => 'Published',
        'published_at' => now(),
        'title' => 'Artikel Related Kurang',
        'content' => '<p>Satu.</p><p>Dua.</p><p>Tiga.</p><p>Empat.</p>',
    ]);

    $post->relatedPosts()->sync($onlyTwo->pluck('id')->all());

    $html = get(route('news.show', $post->slug))
        ->assertSuccessful()
        ->getContent();

    expect(substr_count($html, 'class="baca-juga-item"'))->toBe(AutoRelatedPosts::LIMIT);
});
