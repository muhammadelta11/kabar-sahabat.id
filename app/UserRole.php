<?php

namespace App;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Direktur = 'direktur';
    case PimpinanRedaktur = 'pimpinan_redaktur';
    case Redaktur = 'redaktur';
    case Kontributor = 'kontributor';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Direktur => 'Direktur',
            self::PimpinanRedaktur => 'Pimpinan Redaktur',
            self::Redaktur => 'Redaktur',
            self::Kontributor => 'Kontributor',
        };
    }

    public function canAccessPanel(): bool
    {
        return match ($this) {
            self::SuperAdmin, self::Direktur, self::PimpinanRedaktur, self::Redaktur => true,
            self::Kontributor => false,
        };
    }

    public function canSubmitArticles(): bool
    {
        return $this === self::Kontributor;
    }

    /**
     * @return list<string>
     */
    public function abilities(): array
    {
        return match ($this) {
            self::SuperAdmin => [
                'manage_users',
                'manage_settings',
                'manage_ads',
                'manage_posts',
                'manage_categories',
                'manage_comments',
                'manage_contributors',
                'view_business_dashboard',
                'view_editorial_dashboard',
                'view_redaktur_performance',
            ],
            self::Direktur => [
                'manage_ads',
                'view_business_dashboard',
                'view_redaktur_performance',
            ],
            self::PimpinanRedaktur => [
                'manage_posts',
                'manage_categories',
                'manage_comments',
                'manage_contributors',
                'view_editorial_dashboard',
                'view_redaktur_performance',
            ],
            self::Redaktur => [
                'manage_posts',
                'manage_categories',
                'manage_comments',
                'manage_contributors',
                'view_editorial_dashboard',
            ],
            self::Kontributor => [],
        };
    }

    public function can(string $ability): bool
    {
        return in_array($ability, $this->abilities(), true);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function staffOptions(): array
    {
        return collect(self::cases())
            ->reject(fn (self $role) => $role === self::Kontributor)
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->all();
    }
}
