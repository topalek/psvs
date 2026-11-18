<?php

namespace App\Filament\Support;

use App\Models\Tariff;
use BackedEnum;

class FieldHelpers
{
    public static function enumLabel(mixed $state): string
    {
        if ($state instanceof BackedEnum && method_exists($state, 'label')) {
            return $state->label();
        }

        return $state === null || $state === '' ? '—' : (string) $state;
    }

    public static function money(mixed $state): string
    {
        return $state === null || $state === '' ? '—' : Tariff::formatMoney((int) $state);
    }

    public static function rublesToMinor(mixed $state): ?int
    {
        if ($state === null || $state === '') {
            return null;
        }

        return (int) round(((float) $state) * 100);
    }

    public static function minorToRubles(mixed $state): ?float
    {
        if ($state === null || $state === '') {
            return null;
        }

        return ((int) $state) / 100;
    }
}
