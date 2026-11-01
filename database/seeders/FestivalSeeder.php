<?php

namespace Database\Seeders;

use App\Enums\FestivalStatus;
use App\Models\Festival;
use App\Models\FestivalSetting;
use Illuminate\Database\Seeder;

class FestivalSeeder extends Seeder
{
    /** Tahun festival yang dibuat oleh seeder; dapat di-override via env FESTIVAL_YEAR. */
    public static function targetYear(): int
    {
        return (int) env('FESTIVAL_YEAR', date('Y'));
    }

    public function run(): void
    {
        $year = static::targetYear();

        $festival = Festival::firstOrCreate(
            ['year' => $year],
            [
                'name' => 'Festival Inovasi '.$year,
                'status' => FestivalStatus::Preparation,
            ],
        );

        FestivalSetting::firstOrCreate(
            ['festival_id' => $festival->id],
            [
                'organizer_name' => env('ORGANIZER_NAME', 'Panitia Festival Inovasi'),
            ],
        );
    }
}
