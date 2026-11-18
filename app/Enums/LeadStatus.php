<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case WaitingCustomer = 'waiting_customer';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Spam = 'spam';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новая',
            self::InProgress => 'В работе',
            self::WaitingCustomer => 'Ожидает клиента',
            self::Completed => 'Завершена',
            self::Rejected => 'Отклонена',
            self::Spam => 'Спам',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'info',
            self::InProgress => 'warning',
            self::WaitingCustomer => 'gray',
            self::Completed => 'success',
            self::Rejected => 'danger',
            self::Spam => 'danger',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
