<?php

namespace App\Support;

class RoleLabels
{
    /** @var array<string, string> */
    public const LABELS = [
        'admin' => 'Администратор',
        'engineer' => 'Инженер',
        'manager' => 'Менеджер',
    ];

    public static function label(?string $role): string
    {
        if ($role === null || $role === '') {
            return '—';
        }

        return self::LABELS[$role] ?? $role;
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return self::LABELS;
    }
}
