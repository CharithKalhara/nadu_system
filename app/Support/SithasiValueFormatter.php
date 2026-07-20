<?php

namespace App\Support;

use Carbon\Carbon;

class SithasiValueFormatter
{
    /** @return array{warshaya: string, masaya: string, dinaya: string} */
    public static function dateParts(?string $date): array
    {
        if (blank($date)) {
            return ['warshaya' => '', 'masaya' => '', 'dinaya' => ''];
        }

        $date = Carbon::parse($date);

        return [
            'warshaya' => (string) $date->year,
            'masaya' => self::sinhalaMonth($date->month),
            'dinaya' => (string) $date->day,
        ];
    }

    public static function waruwa(?string $time): string
    {
        return blank($time) ? '' : (Carbon::parse($time)->hour < 12 ? 'පෙරවරු' : 'පස්වරු');
    }

    public static function time(?string $time): string
    {
        return blank($time) ? '' : Carbon::parse($time)->format('H:i');
    }

    private static function sinhalaMonth(int $month): string
    {
        return [
            1 => 'ජනවාරි', 2 => 'පෙබරවාරි', 3 => 'මාර්තු', 4 => 'අප්‍රේල්',
            5 => 'මැයි', 6 => 'ජූනි', 7 => 'ජූලි', 8 => 'අගෝස්තු',
            9 => 'සැප්තැම්බර්', 10 => 'ඔක්තෝබර්', 11 => 'නොවැම්බර්', 12 => 'දෙසැම්බර්',
        ][$month];
    }
}
