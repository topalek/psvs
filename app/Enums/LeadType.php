<?php

namespace App\Enums;

enum LeadType: string
{
    case Connection = 'connection';
    case CoverageCheck = 'coverage_check';
    case Business = 'business';
    case Contact = 'contact';
    case Callback = 'callback';

    public function label(): string
    {
        return match ($this) {
            self::Connection => 'Заявка на подключение',
            self::CoverageCheck => 'Проверка адреса',
            self::Business => 'Заявка для бизнеса',
            self::Contact => 'Обращение с сайта',
            self::Callback => 'Обратный звонок',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
