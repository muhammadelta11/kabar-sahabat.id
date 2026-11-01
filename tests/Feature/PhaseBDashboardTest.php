<?php

use App\Filament\Pages\AnalyticsDashboard;
use App\Models\Advertisement;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('mengizinkan direktur membuka halaman analytics', function () {
    actingAs(User::factory()->direktur()->create())
        ->get(AnalyticsDashboard::getUrl())
        ->assertSuccessful();
});

it('menolak redaktur dari halaman analytics bisnis', function () {
    actingAs(User::factory()->redaktur()->create())
        ->get(AnalyticsDashboard::getUrl())
        ->assertForbidden();
});

it('menampilkan riwayat pengajuan milik kontributor', function () {
    $user = User::factory()->kontributor()->create();

    Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Naskah Kontributor Satu',
        'status' => 'Menunggu Review',
    ]);

    actingAs($user)
        ->get(route('submissions.index'))
        ->assertSuccessful()
        ->assertSee('Naskah Kontributor Satu')
        ->assertSee('Menunggu Review');
});

it('menghitung estimasi mrr dari iklan aktif', function () {
    Advertisement::query()->create([
        'slot_key' => 'sidebar_mrec',
        'name' => 'Sponsor A',
        'client_name' => 'Brand A',
        'type' => 'image',
        'is_active' => true,
        'monthly_rate' => 1500000,
    ]);

    Advertisement::query()->create([
        'slot_key' => 'header_leaderboard',
        'name' => 'Sponsor B',
        'type' => 'image',
        'is_active' => false,
        'monthly_rate' => 900000,
    ]);

    $mrr = (int) Advertisement::query()
        ->where('is_active', true)
        ->whereNotNull('monthly_rate')
        ->sum('monthly_rate');

    expect($mrr)->toBe(1500000);
});

it('menyimpan measurement id ga di settings', function () {
    $admin = User::factory()->superAdmin()->create();
    Setting::query()->create([
        'tagline' => 'Tagline',
        'copyright' => 'Copyright',
    ]);

    actingAs($admin)
        ->post(route('admin.settings.update'), [
            'tagline' => 'Tagline',
            'copyright' => 'Copyright',
            'ga_measurement_id' => 'G-ABC123XYZ',
            'ga_dashboard_url' => 'https://analytics.google.com/analytics/web/',
            'analytics_code' => '',
        ])
        ->assertRedirect();

    $setting = Setting::query()->first();

    expect($setting->ga_measurement_id)->toBe('G-ABC123XYZ')
        ->and($setting->ga_dashboard_url)->toContain('analytics.google.com');
});
