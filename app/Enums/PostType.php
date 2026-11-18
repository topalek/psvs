<?php

namespace App\Enums;

enum PostType: string
{
    case CompanyNews = 'company_news';
    case TariffChange = 'tariff_change';
    case Maintenance = 'maintenance';
    case Outage = 'outage';
    case CoverageExpansion = 'coverage_expansion';

    public function label(): string
    {
        return match ($this) {
            self::CompanyNews => 'Новости компании',
            self::TariffChange => 'Изменение тарифов и условий',
            self::Maintenance => 'Плановые работы',
            self::Outage => 'Авария / сбой',
            self::CoverageExpansion => 'Расширение сети',
        };
    }

    public function isOperational(): bool
    {
        return in_array($this, [self::Maintenance, self::Outage], true);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
