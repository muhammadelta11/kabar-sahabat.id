<?php

use App\Filament\Widgets\RedakturPerformanceTable;
use App\Models\Post;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('mengizinkan pimpinan redaktur dan direktur melihat performa', function (UserRole $role) {
    actingAs(User::factory()->create(['role' => $role]));

    expect(RedakturPerformanceTable::canView())->toBeTrue();
})->with([
    UserRole::PimpinanRedaktur,
    UserRole::Direktur,
    UserRole::SuperAdmin,
]);

it('menolak redaktur biasa dari widget performa', function () {
    actingAs(User::factory()->redaktur()->create());

    expect(RedakturPerformanceTable::canView())->toBeFalse();
});

it('menghitung unggahan hari ini per redaktur', function () {
    $redakturA = User::factory()->redaktur()->create(['name' => 'Redaktur A']);
    $redakturB = User::factory()->redaktur()->create(['name' => 'Redaktur B']);

    Post::factory()->count(3)->create([
        'user_id' => $redakturA->id,
        'created_at' => now(),
        'status' => 'Published',
        'published_at' => now(),
    ]);

    Post::factory()->create([
        'user_id' => $redakturB->id,
        'created_at' => now()->subDays(3),
        'status' => 'Published',
        'published_at' => now()->subDays(3),
    ]);

    $today = now()->toDateString();
    $weekStart = now()->startOfWeek()->toDateTimeString();

    $rows = User::query()
        ->whereIn('role', [UserRole::Redaktur, UserRole::PimpinanRedaktur])
        ->withCount([
            'posts as uploaded_today' => fn (Builder $query) => $query->whereDate('created_at', $today),
            'posts as published_today' => fn (Builder $query) => $query
                ->where('status', 'Published')
                ->whereDate('published_at', $today),
            'posts as uploaded_week' => fn (Builder $query) => $query->where('created_at', '>=', $weekStart),
        ])
        ->get()
        ->keyBy('id');

    expect($rows[$redakturA->id]->uploaded_today)->toBe(3)
        ->and($rows[$redakturA->id]->published_today)->toBe(3)
        ->and($rows[$redakturB->id]->uploaded_today)->toBe(0)
        ->and($rows[$redakturB->id]->uploaded_week)->toBe(1);

    actingAs(User::factory()->direktur()->create());

    RedakturPerformanceTable::forgetCache();

    Livewire::test(RedakturPerformanceTable::class)
        ->assertSee('Redaktur A')
        ->assertSee('Redaktur B');

    $cached = RedakturPerformanceTable::cachedRows()->keyBy('id');

    expect($cached[$redakturA->id]->uploaded_today)->toBe(3)
        ->and($cached[$redakturA->id]->published_today)->toBe(3)
        ->and($cached[$redakturB->id]->uploaded_today)->toBe(0);
});
