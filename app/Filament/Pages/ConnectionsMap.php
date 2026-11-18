<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ConnectionsMap extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|\UnitEnum|null $navigationGroup = 'Сервис';

    protected static ?string $navigationLabel = 'Карта сети';

    protected static ?int $navigationSort = 70;

    protected string $view = 'filament.pages.connections-map';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('engineer_workspace.access') ?? false;
    }

    public function getTitle(): string
    {
        return 'Карта сети';
    }
}
