<?php

namespace App;

enum UserAccountStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Active => 'Aktif',
            self::Rejected => 'Ditolak',
        };
    }
}
