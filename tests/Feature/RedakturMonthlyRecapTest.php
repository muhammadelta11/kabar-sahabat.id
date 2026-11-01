<?php

use App\Filament\Pages\RekapanRedaktur;
use App\Models\Post;
use App\Models\User;
use App\Support\RedakturMonthlyRecap;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('menghitung skor dari jumlah, konsistensi, dan viewers', function () {
    $month = now()->format('Y-m');
    $monthStart = RedakturMonthlyRecap::parseMonth($month);

    $perfect = RedakturMonthlyRecap::score(
        published: RedakturMonthlyRecap::TARGET_PUBLISHED,
        activeDays: RedakturMonthlyRecap::consistencyDenominator($monthStart),
        views: RedakturMonthlyRecap::TARGET_VIEWS,
        monthStart: $monthStart,
    );

    $empty = RedakturMonthlyRecap::score(0, 0, 0, $monthStart);

    expect($perfect)->toBe(100.0)
        ->and($empty)->toBe(0.0);
});

it('merekap terbit, hari aktif, dan views per redaktur bulan ini', function () {
    $redaktur = User::factory()->redaktur()->create(['name' => 'Redaktur Bulanan']);

    Post::factory()->create([
        'user_id' => $redaktur->id,
        'status' => 'Published',
        'published_at' => now()->startOfMonth()->addDays(1),
        'views_count' => 400,
    ]);

    Post::factory()->create([
        'user_id' => $redaktur->id,
        'status' => 'Published',
        'published_at' => now()->startOfMonth()->addDays(3),
        'views_count' => 600,
    ]);

    Post::factory()->create([
        'user_id' => $redaktur->id,
        'status' => 'Draft',
        'published_at' => null,
        'views_count' => 999,
    ]);

    $row = RedakturMonthlyRecap::rows(now()->format('Y-m'))
        ->firstWhere(fn (array $item): bool => $item['user']->is($redaktur));

    expect($row)->not->toBeNull()
        ->and($row['published'])->toBe(2)
        ->and($row['active_days'])->toBe(2)
        ->and($row['views'])->toBe(1000)
        ->and($row['score'])->toBeGreaterThan(0);
});

it('mengizinkan direktur dan pimpinan membuka halaman rekapan', function (UserRole $role) {
    actingAs(User::factory()->create(['role' => $role]));

    get(RekapanRedaktur::getUrl())->assertSuccessful();
})->with([
    UserRole::Direktur,
    UserRole::PimpinanRedaktur,
]);

it('menolak redaktur biasa dari halaman rekapan', function () {
    actingAs(User::factory()->redaktur()->create());

    get(RekapanRedaktur::getUrl())->assertForbidden();
});
