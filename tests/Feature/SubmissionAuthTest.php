<?php

use App\Models\User;
use App\UserAccountStatus;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\from;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('mengalihkan tamu dari kirim artikel ke halaman masuk', function () {
    get(route('submissions.create'))
        ->assertRedirect(route('auth.login'));
});

it('mengizinkan kontributor aktif membuka form kirim artikel', function () {
    $user = User::factory()->kontributor()->create();

    actingAs($user)
        ->get(route('submissions.create'))
        ->assertSuccessful();
});

it('mengalihkan kontributor pending ke halaman menunggu', function () {
    $user = User::factory()->kontributor()->pending()->create();

    actingAs($user)
        ->get(route('submissions.create'))
        ->assertRedirect(route('auth.pending'));
});

it('mengalihkan kontributor ditolak ke halaman menunggu', function () {
    $user = User::factory()->kontributor()->rejected()->create();

    actingAs($user)
        ->get(route('submissions.create'))
        ->assertRedirect(route('auth.pending'));
});

it('mengalihkan staf dari form publik ke panel admin', function () {
    $user = User::factory()->create(['role' => UserRole::Redaktur]);

    actingAs($user)
        ->get(route('submissions.create'))
        ->assertRedirect('/admin');
});

it('mendaftarkan akun baru sebagai kontributor pending', function () {
    post(route('auth.register.submit'), [
        'name' => 'Siti Kontributor',
        'email' => 'siti@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'agreement' => '1',
    ])
        ->assertRedirect(route('auth.pending'));

    $user = User::query()->where('email', 'siti@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe(UserRole::Kontributor)
        ->and($user->account_status)->toBe(UserAccountStatus::Pending)
        ->and(auth()->id())->toBe($user->id);
});

it('menyimpan intended url setelah login kontributor aktif', function () {
    $user = User::factory()->kontributor()->create([
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    from(route('submissions.create'))
        ->get(route('submissions.create'))
        ->assertRedirect(route('auth.login'));

    post(route('auth.login.submit'), [
        'email' => 'login@example.com',
        'password' => 'password',
    ])->assertRedirect(route('submissions.create'));

    expect(auth()->id())->toBe($user->id);
});

it('mengalihkan login kontributor pending ke halaman menunggu', function () {
    User::factory()->kontributor()->pending()->create([
        'email' => 'pending@example.com',
        'password' => 'password',
    ]);

    post(route('auth.login.submit'), [
        'email' => 'pending@example.com',
        'password' => 'password',
    ])->assertRedirect(route('auth.pending'));
});
