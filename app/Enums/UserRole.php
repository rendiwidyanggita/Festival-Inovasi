<?php

namespace App\Enums;

enum UserRole: string
{
    case Superadmin = 'superadmin';
    case Jury = 'jury';

    public function label(): string
    {
        return match ($this) {
            self::Superadmin => 'Superadmin',
            self::Jury => 'Juri',
        };
    }
}
