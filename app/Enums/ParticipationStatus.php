<?php

namespace App\Enums;

enum ParticipationStatus: string
{
    case Selected = 'selected';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Selected => 'Terpilih',
            self::InProgress => 'Sedang Dinilai',
            self::Completed => 'Selesai',
        };
    }
}
