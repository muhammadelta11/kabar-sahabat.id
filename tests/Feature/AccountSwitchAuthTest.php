<?php

use App\Filament\Auth\Login;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;

uses(RefreshDatabase::class);

it('menampilkan form masuk meski kontributor sudah login', function () {
    $user = User::factory()->kontributor()->create(['name' => 'Budi Kontributor']);

    actingAs($user)
        ->get(route('auth.login'))
        ->assertSuccessful()
        ->assertSee('Budi Kontributor')
        ->assertSee('Ganti akun & masuk');
});

it('mengganti sesi kontributor ke redaktur lewat /masuk', function () {
    $kontributor = User::factory()->kontributor()->create([
        'email' => 'kontributor@example.com',
        'password' => 'password',
    ]);
    $redaktur = User::factory()->redaktur()->create([
        'email' => 'redaktur@example.com',
        'password' => 'password',
    ]);

    actingAs($kontributor)
        ->post(route('auth.login.submit'), [
            'email' => 'redaktur@example.com',
            'password' => 'password',
        ])
        ->assertRedirect('/admin');

    assertAuthenticatedAs($redaktur);
});

it('mengalihkan kontributor dari /admin ke login panel (bukan 403)', function () {
    $user = User::factory()->kontributor()->create();

    actingAs($user)
        ->get('/admin')
        ->assertRedirect('/admin/login');

    assertGuest();
});

it('menampilkan login panel saat kontributor membuka /admin/login', function () {
    $user = User::factory()->kontributor()->create();

    actingAs($user)
        ->get('/admin/login')
        ->assertSuccessful()
        ->assertSee('Masuk Panel Redaksi');

    assertGuest();
});

it('mengizinkan redaktur login di panel setelah sesi kontributor', function () {
    $kontributor = User::factory()->kontributor()->create();
    $redaktur = User::factory()->redaktur()->create([
        'email' => 'panel@example.com',
        'password' => 'password',
    ]);

    actingAs($kontributor);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => 'panel@example.com',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');

    assertAuthenticatedAs($redaktur);
    expect(auth()->user()->role)->toBe(UserRole::Redaktur);
});

it('mengalihkan staf yang sudah login dari /masuk ke panel', function () {
    $user = User::factory()->redaktur()->create();

    actingAs($user)
        ->get(route('auth.login'))
        ->assertRedirect('/admin');
});
