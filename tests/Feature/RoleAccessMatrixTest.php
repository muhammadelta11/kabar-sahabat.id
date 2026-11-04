<?php

use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\AdvertisementResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CommentResource;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\BusinessStatsOverview;
use App\Filament\Widgets\EditorialQueueOverview;
use App\Filament\Widgets\PopularPostsTable;
use App\Filament\Widgets\RedakturPerformanceTable;
use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('memberi matriks akses resource sesuai role', function (UserRole $role, array $expected) {
    actingAs(User::factory()->create(['role' => $role]));

    expect(UserResource::canViewAny())->toBe($expected['users'])
        ->and(ManageSettings::canAccess())->toBe($expected['settings'])
        ->and(AdvertisementResource::canViewAny())->toBe($expected['ads'])
        ->and(PostResource::canViewAny())->toBe($expected['posts'])
        ->and(CategoryResource::canViewAny())->toBe($expected['categories'])
        ->and(CommentResource::canViewAny())->toBe($expected['comments']);
})->with([
    'super_admin' => [UserRole::SuperAdmin, [
        'users' => true, 'settings' => true, 'ads' => true,
        'posts' => true, 'categories' => true, 'comments' => true,
    ]],
    'direktur' => [UserRole::Direktur, [
        'users' => false, 'settings' => false, 'ads' => true,
        'posts' => false, 'categories' => false, 'comments' => false,
    ]],
    'pimpinan_redaktur' => [UserRole::PimpinanRedaktur, [
        'users' => false, 'settings' => false, 'ads' => false,
        'posts' => true, 'categories' => true, 'comments' => true,
    ]],
    'redaktur' => [UserRole::Redaktur, [
        'users' => false, 'settings' => false, 'ads' => false,
        'posts' => true, 'categories' => true, 'comments' => true,
    ]],
]);

it('menampilkan widget dashboard sesuai role', function (UserRole $role, array $expected) {
    actingAs(User::factory()->create(['role' => $role]));

    expect(BusinessStatsOverview::canView())->toBe($expected['business'])
        ->and(EditorialQueueOverview::canView())->toBe($expected['editorial'])
        ->and(StatsOverview::canView())->toBe($expected['readers'])
        ->and(PopularPostsTable::canView())->toBe($expected['readers'])
        ->and(RedakturPerformanceTable::canView())->toBe($expected['performance']);
})->with([
    'super_admin' => [UserRole::SuperAdmin, [
        'business' => true, 'editorial' => true, 'readers' => true, 'performance' => true,
    ]],
    'direktur' => [UserRole::Direktur, [
        'business' => true, 'editorial' => false, 'readers' => true, 'performance' => true,
    ]],
    'pimpinan_redaktur' => [UserRole::PimpinanRedaktur, [
        'business' => false, 'editorial' => true, 'readers' => true, 'performance' => true,
    ]],
    'redaktur' => [UserRole::Redaktur, [
        'business' => false, 'editorial' => true, 'readers' => true, 'performance' => false,
    ]],
]);
