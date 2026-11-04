<?php

use App\Filament\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('redaktur hanya melihat komentar di berita miliknya', function () {
    $redaktur = User::factory()->redaktur()->create();
    $lain = User::factory()->redaktur()->create();

    $milik = Post::factory()->create([
        'user_id' => $redaktur->id,
        'title' => 'Berita Saya',
        'status' => 'Published',
        'published_at' => now(),
    ]);

    $bukan = Post::factory()->create([
        'user_id' => $lain->id,
        'title' => 'Berita Orang Lain',
        'status' => 'Published',
        'published_at' => now(),
    ]);

    Comment::query()->create([
        'post_id' => $milik->id,
        'guest_name' => 'Pembaca A',
        'content' => 'Komentar di berita saya',
        'is_approved' => true,
    ]);

    Comment::query()->create([
        'post_id' => $bukan->id,
        'guest_name' => 'Pembaca B',
        'content' => 'Komentar di berita orang lain',
        'is_approved' => true,
    ]);

    actingAs($redaktur);

    $ids = CommentResource::getEloquentQuery()->pluck('post_id')->all();

    expect($ids)->toContain($milik->id)
        ->and($ids)->not->toContain($bukan->id);
});

it('pimpinan redaktur melihat semua komentar', function () {
    $pimpinan = User::factory()->create(['role' => UserRole::PimpinanRedaktur]);
    $redaktur = User::factory()->redaktur()->create();

    $post = Post::factory()->create([
        'user_id' => $redaktur->id,
        'status' => 'Published',
        'published_at' => now(),
    ]);

    Comment::query()->create([
        'post_id' => $post->id,
        'guest_name' => 'Pembaca',
        'content' => 'Komentar umum',
        'is_approved' => false,
    ]);

    actingAs($pimpinan);

    expect(CommentResource::getEloquentQuery()->count())->toBe(1);
});
