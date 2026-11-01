<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('mengizinkan kontributor membuka halaman akun', function () {
    $user = User::factory()->kontributor()->create(['name' => 'Siti Kontributor']);

    actingAs($user)
        ->get(route('account.edit'))
        ->assertSuccessful()
        ->assertSee('Akun Saya')
        ->assertSee('Siti Kontributor')
        ->assertSee('Keluar / Logout');
});

it('memperbarui nama dan password kontributor', function () {
    $user = User::factory()->kontributor()->create([
        'name' => 'Nama Lama',
        'password' => Hash::make('password'),
    ]);

    actingAs($user)
        ->put(route('account.update'), [
            'name' => 'Nama Baru',
            'current_password' => 'password',
            'password' => 'password-baru1',
            'password_confirmation' => 'password-baru1',
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->name)->toBe('Nama Baru')
        ->and(Hash::check('password-baru1', $user->password))->toBeTrue();
});

it('menolak non-kontributor dari halaman akun', function () {
    $user = User::factory()->redaktur()->create();

    actingAs($user)
        ->get(route('account.edit'))
        ->assertRedirect(route('news.index'));
});
