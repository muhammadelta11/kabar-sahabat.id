<?php

use App\Models\User;
use App\UserRole;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('memetakan label dan opsi role', function () {
    expect(UserRole::options())->toMatchArray([
        'super_admin' => 'Super Admin',
        'direktur' => 'Direktur',
        'pimpinan_redaktur' => 'Pimpinan Redaktur',
        'redaktur' => 'Redaktur',
        'kontributor' => 'Kontributor',
    ]);
});

it('membatasi akses panel filament untuk kontributor', function () {
    $panel = Panel::make()->id('admin');

    $staff = User::factory()->create(['role' => UserRole::Redaktur]);
    $kontributor = User::factory()->kontributor()->create();

    expect($staff->canAccessPanel($panel))->toBeTrue()
        ->and($kontributor->canAccessPanel($panel))->toBeFalse()
        ->and($kontributor->isKontributor())->toBeTrue();
});

it('mengenali super admin', function () {
    $user = User::factory()->superAdmin()->create();

    expect($user->isSuperAdmin())->toBeTrue()
        ->and($user->role)->toBe(UserRole::SuperAdmin);
});
