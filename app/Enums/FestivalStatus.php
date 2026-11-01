<?php

namespace App\Enums;

enum FestivalStatus: string
{
    case Preparation = 'preparation';
    case Ongoing = 'ongoing';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Preparation => 'Persiapan',
            self::Ongoing => 'Berlangsung',
            self::Completed => 'Selesai',
        };
    }
}
