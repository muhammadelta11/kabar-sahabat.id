<?php

use App\Filament\Pages\EditorialPerformanceHub;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Setting;
use App\Models\User;
use App\Support\EditorialInsights;
use App\Support\RedakturOperationalRecap;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
});

it('mengizinkan direktur dan pimpinan redaktur membuka hub performa', function (UserRole $role) {
    actingAs(User::factory()->create(['role' => $role]));

    expect(EditorialPerformanceHub::canAccess())->toBeTrue();

    Livewire::test(EditorialPerformanceHub::class)
        ->assertSuccessful()
        ->assertSee('Operasional harian')
        ->assertSee('Rekapan bulanan')
        ->assertSee('Top 10 berita')
        ->assertSee('Google Analytics');
})->with([
    UserRole::Direktur,
    UserRole::PimpinanRedaktur,
    UserRole::SuperAdmin,
]);

it('menolak redaktur biasa dari hub performa', function () {
    actingAs(User::factory()->redaktur()->create());

    expect(EditorialPerformanceHub::canAccess())->toBeFalse();

    get(EditorialPerformanceHub::getUrl())->assertForbidden();
});

it('menampilkan metrik operasional dan top viewer di hub', function () {
    $redaktur = User::factory()->redaktur()->create(['name' => 'Rina Redaktur']);
    $category = Category::query()->create(['name' => 'Politik', 'slug' => 'politik']);

    $hot = Post::factory()->create([
        'user_id' => $redaktur->id,
        'status' => 'Published',
        'published_at' => now()->subHour(),
        'title' => 'Berita Paling Laris Hub',
        'views_count' => 9001,
    ]);
    $hot->categories()->attach($category);

    PostView::query()->create([
        'post_id' => $hot->id,
        'viewed_at' => now()->subDay(),
    ]);

    actingAs(User::factory()->direktur()->create());

    $ops = RedakturOperationalRecap::rows()->firstWhere(fn ($row) => $row['user']->id === $redaktur->id);
    expect($ops)->not->toBeNull()
        ->and($ops['published_today'])->toBeGreaterThanOrEqual(1);

    expect(EditorialInsights::topPostsAllTime(5)->first()?->title)->toBe('Berita Paling Laris Hub');

    Livewire::test(EditorialPerformanceHub::class)
        ->assertSee('Rina Redaktur')
        ->assertSee('Berita Paling Laris Hub')
        ->assertSee('Politik');
});

it('memakai measurement id dari settings pada layout publik', function () {
    $setting = Setting::query()->first() ?? Setting::query()->create([
        'tagline' => 'Test',
        'copyright' => 'Test',
    ]);
    $setting->update(['ga_measurement_id' => 'G-TESTHUB123']);
    Cache::flush();

    get('/')
        ->assertSuccessful()
        ->assertSee('G-TESTHUB123', false)
        ->assertDontSee('gtag/js?id=G-W85WZ4589W', false);
});
