<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case Unverified = 'unverified';
    case Verified = 'verified';

    public function label(): string
    {
        return match ($this) {
            self::Unverified => 'Требует проверки',
            self::Verified => 'Проверено',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
