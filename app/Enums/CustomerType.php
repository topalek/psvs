<?php

namespace App\Enums;

enum CustomerType: string
{
    case Residential = 'residential';
    case Business = 'business';

    public function label(): string
    {
        return match ($this) {
            self::Residential => 'Для дома',
            self::Business => 'Для бизнеса',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
