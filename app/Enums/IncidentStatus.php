<?php

namespace App\Enums;

enum IncidentStatus: string
{
    case Planned = 'planned';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Запланировано',
            self::InProgress => 'Выполняется',
            self::Resolved => 'Завершено',
            self::Cancelled => 'Отменено',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Planned, self::InProgress], true);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
