<?php

namespace App\Enums;

enum ScoringStatus: string
{
    case Preparation = 'preparation';
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Preparation => 'Persiapan',
            self::Open => 'Penilaian Dibuka',
            self::Closed => 'Ditutup',
        };
    }
}
