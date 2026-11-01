<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserAccountStatus;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleReviewSeeder extends Seeder
{
    /**
     * Akun tinjauan per role. Password semua: password
     *
     * @var array<int, array{email: string, name: string, role: UserRole}>
     */
    private array $accounts = [
        ['email' => 'superadmin@kabarsahabat.test', 'name' => 'Super Admin Review', 'role' => UserRole::SuperAdmin],
        ['email' => 'direktur@kabarsahabat.test', 'name' => 'Direktur Review', 'role' => UserRole::Direktur],
        ['email' => 'pimpinan@kabarsahabat.test', 'name' => 'Pimpinan Redaktur Review', 'role' => UserRole::PimpinanRedaktur],
        ['email' => 'redaktur@kabarsahabat.test', 'name' => 'Redaktur Review', 'role' => UserRole::Redaktur],
        ['email' => 'kontributor@kabarsahabat.test', 'name' => 'Kontributor Review', 'role' => UserRole::Kontributor],
    ];

    public function run(): void
    {
        $password = Hash::make('password');

        foreach ($this->accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => $password,
                    'role' => $account['role'],
                    'account_status' => UserAccountStatus::Active,
                ],
            );
        }

        // Legacy email dari seeder lama → pimpinan redaktur
        User::query()->where('email', 'admin@kabarsahabat.test')->update([
            'role' => UserRole::PimpinanRedaktur,
            'name' => 'Pimpinan Redaktur (legacy admin)',
        ]);

        $this->command?->info('Akun tinjauan role siap. Password semua: password');
        foreach ($this->accounts as $account) {
            $panel = $account['role']->canAccessPanel() ? '/admin' : '/kirim-artikel (via /masuk)';
            $this->command?->line("  {$account['role']->value}: {$account['email']} → {$panel}");
        }
    }
}
