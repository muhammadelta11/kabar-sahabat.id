<?php

namespace Database\Factories;

use App\Models\User;
use App\UserAccountStatus;
use App\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Redaktur,
            'account_status' => UserAccountStatus::Active,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::SuperAdmin,
        ]);
    }

    public function direktur(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Direktur,
        ]);
    }

    public function pimpinanRedaktur(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::PimpinanRedaktur,
        ]);
    }

    public function redaktur(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Redaktur,
        ]);
    }

    public function kontributor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Kontributor,
            'account_status' => UserAccountStatus::Active,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_status' => UserAccountStatus::Pending,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_status' => UserAccountStatus::Rejected,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
