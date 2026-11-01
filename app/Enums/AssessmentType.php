<?php

namespace App\Enums;

enum AssessmentType: string
{
    case Innovation = 'innovation';
    case Institution = 'institution';

    public function label(): string
    {
        return match ($this) {
            self::Innovation => 'Inovasi',
            self::Institution => 'Instansi',
        };
    }
}
