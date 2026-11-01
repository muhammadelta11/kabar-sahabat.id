<?php

use App\Filament\Resources\ContributorApplicationResource;
use App\Models\User;
use App\UserAccountStatus;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('membatasi kirim artikel hanya untuk kontributor aktif', function () {
    $pending = User::factory()->kontributor()->pending()->create();
    $active = User::factory()->kontributor()->create();

    expect($pending->canSubmitArticles())->toBeFalse()
        ->and($active->canSubmitArticles())->toBeTrue();
});

it('menampilkan halaman menunggu untuk kontributor pending', function () {
    $user = User::factory()->kontributor()->pending()->create();

    actingAs($user)
        ->get(route('auth.pending'))
        ->assertSuccessful()
        ->assertSee('Menunggu persetujuan redaksi');
});

it('mengalihkan kontributor aktif dari halaman menunggu ke form kirim', function () {
    $user = User::factory()->kontributor()->create();

    actingAs($user)
        ->get(route('auth.pending'))
        ->assertRedirect(route('submissions.create'));
});

it('menyetujui pengajuan mengubah status menjadi aktif', function () {
    $user = User::factory()->kontributor()->pending()->create();

    $user->update(['account_status' => UserAccountStatus::Active]);

    expect($user->fresh()->canSubmitArticles())->toBeTrue()
        ->and($user->fresh()->account_status)->toBe(UserAccountStatus::Active);
});

it('menolak pengajuan mencegah akses form kirim', function () {
    $user = User::factory()->kontributor()->create();

    $user->update(['account_status' => UserAccountStatus::Rejected]);

    actingAs($user->fresh())
        ->get(route('submissions.create'))
        ->assertRedirect(route('auth.pending'));
});

it('mengizinkan redaksi membuka daftar pengajuan akun', function () {
    actingAs(User::factory()->redaktur()->create())
        ->get(ContributorApplicationResource::getUrl())
        ->assertSuccessful();
});

it('menolak direktur dari daftar pengajuan akun', function () {
    actingAs(User::factory()->direktur()->create())
        ->get(ContributorApplicationResource::getUrl())
        ->assertForbidden();
});

it('menampilkan badge jumlah pending di navigasi resource', function () {
    User::factory()->kontributor()->pending()->count(2)->create();
    User::factory()->kontributor()->create();

    actingAs(User::factory()->pimpinanRedaktur()->create());

    expect(ContributorApplicationResource::getNavigationBadge())->toBe('2');
});

it('memberi kemampuan manage_contributors pada role redaksi', function () {
    expect(UserRole::SuperAdmin->can('manage_contributors'))->toBeTrue()
        ->and(UserRole::PimpinanRedaktur->can('manage_contributors'))->toBeTrue()
        ->and(UserRole::Redaktur->can('manage_contributors'))->toBeTrue()
        ->and(UserRole::Direktur->can('manage_contributors'))->toBeFalse()
        ->and(UserRole::Kontributor->can('manage_contributors'))->toBeFalse();
});
