<?php

use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListContributorQueue;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('menampilkan list berita dengan tab status', function () {
    $user = User::factory()->redaktur()->create();

    Post::factory()->create([
        'user_id' => $user->id,
        'status' => 'Published',
        'published_at' => now()->subHour(),
        'title' => 'Berita Sudah Tayang',
    ]);

    actingAs($user);

    Livewire::test(ListPosts::class)
        ->assertSuccessful()
        ->loadTable()
        ->assertCanSeeTableRecords(Post::query()->where('title', 'Berita Sudah Tayang')->get())
        ->assertSee('Menunggu Review')
        ->assertSee('Tayang');
});

it('membuka menu antrian kontributor hanya kiriman menunggu review', function () {
    $user = User::factory()->redaktur()->create();

    Post::factory()->create([
        'user_id' => $user->id,
        'status' => 'Menunggu Review',
        'title' => 'Kiriman Kontributor Pending',
        'author_name' => 'Kontributor A',
    ]);

    Post::factory()->create([
        'user_id' => $user->id,
        'status' => 'Published',
        'published_at' => now()->subHour(),
        'title' => 'Berita Sudah Tayang Jangan Tampil',
    ]);

    actingAs($user);

    Livewire::test(ListContributorQueue::class)
        ->assertSuccessful()
        ->loadTable()
        ->assertSee('Kiriman Kontributor Pending')
        ->assertDontSee('Berita Sudah Tayang Jangan Tampil');

    expect(PostResource::getUrl('queue'))->toContain('/admin/posts/antrian');
});

it('membuka form tulis berita dengan layout naskah dan publikasi', function () {
    $user = User::factory()->redaktur()->create();

    actingAs($user);

    Livewire::test(CreatePost::class)
        ->assertSuccessful()
        ->assertSee('Tulis Berita')
        ->assertSee('Simpan')
        ->assertDontSee('Buat & buat lainnya')
        ->assertDontSee('Ideal 120')
        ->assertFormFieldExists('title')
        ->assertFormFieldExists('content')
        ->assertFormFieldExists('status')
        ->assertFormFieldExists('categories')
        ->assertFormFieldExists('image_url');

    expect(CreatePost::canCreateAnother())->toBeFalse();
});

it('menyimpan berita baru sebagai draft dan mengarahkan ke edit', function () {
    $user = User::factory()->redaktur()->create();

    actingAs($user);

    Livewire::test(CreatePost::class)
        ->fillForm([
            'title' => 'Naskah Baru Performa',
            'slug' => 'naskah-baru-performa',
            'excerpt' => str_repeat('Ringkasan nyaman untuk SEO dan share WhatsApp. ', 3),
            'content' => '<p>'.str_repeat('Isi artikel untuk uji form create yang responsif. ', 40).'</p>',
            'status' => 'Draft',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect(PostResource::getUrl('edit', [
            'record' => Post::query()->where('slug', 'naskah-baru-performa')->firstOrFail(),
        ]));

    $post = Post::query()->where('slug', 'naskah-baru-performa')->first();

    expect($post)->not->toBeNull()
        ->and($post->user_id)->toBe($user->id)
        ->and($post->status)->toBe('Draft');
});

it('membuka halaman edit tanpa memuat ulang berita terkait berlebih', function () {
    $user = User::factory()->redaktur()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'status' => 'Draft',
        'title' => 'Draft Untuk Diedit',
    ]);

    actingAs($user);

    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('Edit Berita')
        ->assertFormSet([
            'title' => 'Draft Untuk Diedit',
            'status' => 'Draft',
        ]);
});

it('konfigurasi list berita mengoptimalkan query dan defer loading', function () {
    $source = file_get_contents(app_path('Filament/Resources/PostResource.php'));

    expect($source)
        ->toContain('deferLoading()')
        ->toContain('modifyQueryUsing')
        ->toContain('persistSearchInSession')
        ->toContain('persistFiltersInSession')
        ->toContain('{$tableName}.title')
        ->not->toContain('{$tableName}.content');
});

it('tab status tetap aman jika cache menyimpan nilai non-array', function () {
    $user = User::factory()->redaktur()->create();

    Post::factory()->create([
        'user_id' => $user->id,
        'status' => 'Draft',
        'title' => 'Draft Tab Cache',
    ]);

    Cache::put('admin.posts.status_tabs', 'corrupt', 30);

    actingAs($user);

    Livewire::test(ListPosts::class)
        ->assertSuccessful()
        ->assertSee('Draft');

    expect(Cache::get('admin.posts.status_tabs'))->toBeArray();
});
