<?php

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\BusinessStatsOverview;
use App\Filament\Widgets\EditorialQueueOverview;
use App\Filament\Widgets\PopularPostsTable;
use App\Filament\Widgets\QuickComposeWidget;
use App\Filament\Widgets\RedakturPerformanceTable;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ViewsChart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('menampilkan beranda admin dengan sapaan dan stylesheet premium', function () {
    $user = User::factory()->redaktur()->create([
        'name' => 'Budi Redaktur',
    ]);

    $html = actingAs($user)
        ->get(Dashboard::getUrl())
        ->assertSuccessful()
        ->getContent();

    expect($html)
        ->toContain('Budi Redaktur')
        ->toContain('Panel Redaktur')
        ->toContain('css/filament-admin.css')
        ->toContain('?v=15')
        ->toContain('ks-brand-logo--light')
        ->toContain('ks-brand-logo--dark')
        ->toContain('images/brand/logo-dark.png')
        ->toContain('Buat Berita')
        ->toContain('ks-mobile-bottom-nav')
        ->toContain('ks-quick-compose')
        ->toContain('ks-quick-compose__btn--primary');
});

it('stylesheet admin memakai token light/dark tanpa mematikan warna stat', function () {
    $css = file_get_contents(public_path('css/filament-admin.css'));

    expect($css)
        ->toContain('.dark {')
        ->toContain('--ks-surface:')
        ->toContain('--ks-card:')
        ->toContain('.fi-modal-window')
        ->toContain('.fi-dropdown-panel')
        ->toContain('.fi-main:has(.fi-resource-create-record-page)')
        ->toContain('.fi-main:has(.fi-resource-edit-record-page)')
        ->toContain('.ks-quick-compose__btn--primary')
        ->toContain('overflow: visible');

    expect(preg_match('/\.fi-wi-stats-overview-stat-value\s*\{[^}]*color\s*:/s', $css))->toBe(0)
        ->and(preg_match('/\.fi-wi-stats-overview-stat-description\s*\{[^}]*color\s*:/s', $css))->toBe(0);
});

it('memakai logo terang di halaman login admin', function () {
    $html = get('/admin/login')
        ->assertSuccessful()
        ->getContent();

    expect($html)
        ->toContain('ks-brand-logo--light')
        ->toContain('fi-simple-layout');
});

it('menampilkan tautan kembali ke beranda di login admin', function () {
    get('/admin/login')
        ->assertSuccessful()
        ->assertSee('Kembali ke beranda', false)
        ->assertSee(url('/'), false);
});

it('memuat widget editorial dan analitik pembaca di beranda', function () {
    actingAs(User::factory()->superAdmin()->create());

    $widgets = (new Dashboard)->getWidgets();

    expect($widgets)
        ->toContain(QuickComposeWidget::class)
        ->toContain(EditorialQueueOverview::class)
        ->toContain(StatsOverview::class)
        ->toContain(BusinessStatsOverview::class)
        ->toContain(ViewsChart::class)
        ->toContain(RedakturPerformanceTable::class)
        ->toContain(PopularPostsTable::class);
});

it('meng-cache baris performa redaktur', function () {
    $redaktur = User::factory()->redaktur()->create(['name' => 'Cache Redaktur']);

    actingAs(User::factory()->direktur()->create());

    $first = RedakturPerformanceTable::cachedRows();
    $second = RedakturPerformanceTable::cachedRows();

    expect($first->pluck('id')->all())
        ->toBe($second->pluck('id')->all())
        ->and($first->firstWhere('id', $redaktur->id)?->name)->toBe('Cache Redaktur');

    Livewire::test(RedakturPerformanceTable::class)
        ->assertSee('Cache Redaktur');
});
