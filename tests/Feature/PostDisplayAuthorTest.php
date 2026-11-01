<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('mengutamakan nama penulis di field penulis', function () {
    $post = Post::factory()->create([
        'penulis' => 'Mushonifin',
        'author_name' => 'Nama Kontributor',
        'user_id' => User::factory()->create(['name' => 'Heromando - Super Admin'])->id,
    ]);

    expect($post->displayAuthorName())->toBe('Mushonifin');
});

it('membersihkan suffix role dari nama akun', function () {
    $post = Post::factory()->create([
        'penulis' => null,
        'author_name' => null,
        'user_id' => User::factory()->create(['name' => 'Heromando - Super Admin'])->id,
    ]);

    expect($post->displayAuthorName())->toBe('Heromando');
});

it('jatuh ke author_name jika penulis kosong', function () {
    $post = Post::factory()->create([
        'penulis' => null,
        'author_name' => 'Budi Kontributor',
        'user_id' => User::factory()->create(['name' => 'Akun Internal'])->id,
    ]);

    expect($post->displayAuthorName())->toBe('Budi Kontributor');
});
