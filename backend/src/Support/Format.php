<?php

declare(strict_types=1);

namespace App\Support;

class Format
{
    public static function currency(int|float|string $value): string
    {
        return number_format((float) $value, 0, ',', ' ') . ' FCFA';
    }

    public static function percent(float $value): string
    {
        $rounded = round($value, 1);
        $prefix = $rounded > 0 ? '+' : '';

        return $prefix . str_replace('.', ',', (string) $rounded) . '%';
    }
}
